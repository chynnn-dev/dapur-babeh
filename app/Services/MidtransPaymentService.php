<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\MidtransPayment;
use App\Models\RestaurantTable;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction as MidtransApi;

class MidtransPaymentService
{
    // Midtrans aktif jika server key dan client key sudah diisi di .env
    public function enabled(): bool
    {
        return filled(config('midtrans.server_key')) && filled(config('midtrans.client_key'));
    }

    private function configure(): void
    {
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = (bool) config('midtrans.is_production');
        Config::$isSanitized  = (bool) config('midtrans.is_sanitized', true);
        Config::$is3ds        = (bool) config('midtrans.is_3ds', true);
    }

    /**
     * Buat pembayaran: hitung total dari database, simpan keranjang, lalu minta Snap token.
     * Transaksi (struk, dapur, laporan) baru dibuat setelah pembayaran terbukti lunas.
     */
    public function create(array $data, ?int $tableId, int $cashierId): MidtransPayment
    {
        $this->configure();

        if ($tableId) {
            $table = RestaurantTable::findOrFail($tableId);

            if ($table->status === 'occupied') {
                throw ValidationException::withMessages(['table_id' => 'Meja sedang terisi, pilih meja lain.']);
            }
        }

        // Harga selalu diambil dari database, bukan dari input klien
        $menus = Menu::whereIn('id', collect($data['items'])->pluck('menu_id'))->get()->keyBy('id');

        $total   = 0;
        $rows    = [];
        $details = [];

        foreach ($data['items'] as $item) {
            $menu = $menus[$item['menu_id']];

            if (! $menu->is_available) {
                throw ValidationException::withMessages(['items' => "{$menu->name} sedang habis."]);
            }

            $price    = (int) $menu->price;
            $qty      = (int) $item['quantity'];
            $subtotal = $price * $qty;
            $total   += $subtotal;

            // Kolom sama dengan yang dipakai CashierController::store()
            $rows[] = [
                'menu_id'  => $menu->id,
                'quantity' => $qty,
                'subtotal' => $subtotal,
                'notes'    => $item['notes'] ?? null,
            ];

            $details[] = [
                'id'       => 'M' . $menu->id,
                'price'    => $price,
                'quantity' => $qty,
                'name'     => Str::limit($menu->name, 45, ''),
            ];
        }

        if ($total < 1) {
            throw ValidationException::withMessages(['items' => 'Total pesanan tidak valid.']);
        }

        $payment = MidtransPayment::create([
            'order_id'     => 'DB-' . now()->format('ymdHis') . '-' . Str::upper(Str::random(4)),
            'cashier_id'   => $cashierId,
            'table_id'     => $tableId,
            'total_amount' => $total,
            'items'        => $rows,
            'status'       => 'pending',
        ]);

        try {
            $token = Snap::getSnapToken([
                'transaction_details' => [
                    'order_id'     => $payment->order_id,
                    'gross_amount' => $total,
                ],
                'item_details' => $details,
            ]);
        } catch (\Throwable $e) {
            report($e);
            $payment->delete();

            throw ValidationException::withMessages([
                'payment' => 'Gagal menghubungi Midtrans. Periksa kunci Midtrans di pengaturan server.',
            ]);
        }

        $payment->update(['snap_token' => $token]);

        return $payment;
    }

    /**
     * Cek status ke Midtrans (sumber yang dipercaya) dan proses jika sudah lunas.
     * Aman dipanggil berulang kali, dari polling kasir maupun notifikasi Midtrans.
     */
    public function sync(MidtransPayment $payment): MidtransPayment
    {
        if ($payment->status !== 'pending') {
            return $payment;
        }

        $this->configure();

        try {
            $status = MidtransApi::status($payment->order_id);
        } catch (\Throwable $e) {
            // 404 = pelanggan belum memilih metode bayar, jadi belum ada transaksi di Midtrans
            if ((int) $e->getCode() === 404) {
                return $payment;
            }

            throw $e;
        }

        return $this->apply($payment, $status);
    }

    // Batalkan pembayaran yang masih menunggu
    public function cancel(MidtransPayment $payment): MidtransPayment
    {
        $payment = $this->sync($payment);   // jangan batalkan yang ternyata sudah dibayar

        if ($payment->status !== 'pending') {
            return $payment;
        }

        $this->configure();

        try {
            MidtransApi::expire($payment->order_id);
        } catch (\Throwable $e) {
            // belum ada transaksi di Midtrans, cukup tandai batal di sisi kita
        }

        $payment->update(['status' => 'cancelled']);

        return $payment;
    }

    private function apply(MidtransPayment $payment, $status): MidtransPayment
    {
        $state = data_get($status, 'transaction_status');
        $fraud = data_get($status, 'fraud_status');
        $type  = data_get($status, 'payment_type');

        $paid = $state === 'settlement'
            || ($state === 'capture' && in_array($fraud, [null, 'accept'], true));

        if ($paid) {
            $gross = (int) round((float) data_get($status, 'gross_amount', 0));

            if ($gross !== (int) $payment->total_amount) {
                Log::warning('Midtrans: nominal tidak cocok', ['order_id' => $payment->order_id, 'gross' => $gross]);
                $payment->update(['status' => 'failed']);

                return $payment;
            }

            $this->finalize($payment->id, $type, $this->describe($status));

            return $payment->refresh();
        }

        $map = ['deny' => 'failed', 'failure' => 'failed', 'cancel' => 'cancelled', 'expire' => 'expired'];

        if (isset($map[$state])) {
            $payment->update(['status' => $map[$state], 'payment_type' => $type]);
        } elseif ($type) {
            $payment->update(['payment_type' => $type]);
        }

        return $payment;
    }

    // Buat transaksi sungguhan (sekali saja, meski dipanggil bersamaan)
    private function finalize(int $paymentId, ?string $paymentType, ?string $detail): void
    {
        DB::transaction(function () use ($paymentId, $paymentType, $detail) {
            $payment = MidtransPayment::lockForUpdate()->findOrFail($paymentId);

            if ($payment->transaction_id) {
                return;
            }

            $attributes = [
                'invoice_number'    => $this->invoiceNumber(),
                'table_id'          => $payment->table_id,
                'cashier_id'        => $payment->cashier_id,
                'total_amount'      => $payment->total_amount,
                'cash_received'     => $payment->total_amount,   // non-tunai: dibayar pas
                'change_amount'     => 0,
                'payment_method'    => $this->localMethod($paymentType),
                'payment_reference' => Str::limit($payment->order_id, 100, ''),
                'status'            => 'paid',
            ];

            // Nama metode asli untuk struk (kolom dibuat oleh migrasi payment_detail)
            if (Schema::hasColumn('transactions', 'payment_detail')) {
                $attributes['payment_detail'] = $detail;
            }

            $transaction = Transaction::create($attributes);

            $transaction->items()->createMany($payment->items);

            if ($payment->table_id) {
                RestaurantTable::lockForUpdate()->find($payment->table_id)?->update(['status' => 'occupied']);
            }

            $payment->update([
                'status'         => 'paid',
                'payment_type'   => $paymentType,
                'transaction_id' => $transaction->id,
            ]);
        });
    }

    // Nama metode pembayaran yang mudah dibaca untuk struk, mis. "Transfer Bank BCA"
    private function describe($status): ?string
    {
        $type = data_get($status, 'payment_type');

        return match ($type) {
            'bank_transfer' => $this->bankLabel($status),
            'echannel'      => 'Transfer Bank Mandiri (Bill)',
            'gopay'         => 'GoPay',
            'shopeepay'     => 'ShopeePay',
            'qris'          => 'QRIS',
            'credit_card'   => $this->cardLabel($status),
            'cstore'        => Str::title((string) data_get($status, 'store', 'Minimarket')),
            default         => $type ? Str::title(str_replace('_', ' ', $type)) : null,
        };
    }

    private function bankLabel($status): string
    {
        $bank = data_get($status, 'va_numbers.0.bank');

        if ($bank) {
            return 'Transfer Bank ' . Str::upper($bank);
        }

        return data_get($status, 'permata_va_number') ? 'Transfer Bank PERMATA' : 'Transfer Bank';
    }

    private function cardLabel($status): string
    {
        $label = data_get($status, 'card_type') === 'debit' ? 'Kartu Debit' : 'Kartu Kredit';
        $bank  = data_get($status, 'bank');

        return $bank ? $label . ' ' . Str::upper($bank) : $label;
    }

    // Laporan hanya mengenal tunai/qris/debit: kartu dicatat Debit, selebihnya QRIS.
    // Nama metode aslinya (mis. Transfer Bank BCA) tersimpan di kolom payment_detail dan tampil di struk.
    private function localMethod(?string $type): string
    {
        return $type === 'credit_card' ? 'debit' : 'qris';
    }

    private function invoiceNumber(): string
    {
        $prefix = 'INV-' . now()->format('Ymd') . '-';
        $count  = Transaction::whereDate('created_at', today())->lockForUpdate()->count() + 1;

        return $prefix . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}