<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\RestaurantTable;
use App\Models\Setting;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CashierController extends Controller
{
    public function index()
    {
        $useTables = Setting::isActive(Setting::TABLE_MANAGEMENT);

        $categories = Category::whereHas('menus', fn ($q) => $q->where('is_available', true))
            ->orderBy('name')
            ->get(['id', 'name']);

        $menus = Menu::where('is_available', true)
            ->orderBy('name')
            ->get()
            ->map(fn ($m) => [
                'id'          => $m->id,
                'name'        => $m->name,
                'price'       => $m->price,
                'category_id' => $m->category_id,
                'image'       => $m->image_url,
            ]);

        $tables    = $useTables ? $this->tableList() : [];
        $methods   = $this->availableMethods();
        $qrisImage = Setting::qrisUrl();

        return view('cashier.pos', compact('useTables', 'categories', 'menus', 'tables', 'methods', 'qrisImage'));
    }

    public function store(Request $request): JsonResponse
    {
        $useTables = Setting::isActive(Setting::TABLE_MANAGEMENT);
        $methods   = $this->availableMethods();

        $data = $request->validate([
            'table_id'          => [$useTables ? 'required' : 'nullable', 'exists:tables,id'],
            'payment_method'    => ['required', Rule::in(array_keys($methods))],
            'cash_received'     => ['required_if:payment_method,cash', 'nullable', 'integer', 'min:0'],
            'payment_reference' => ['nullable', 'string', 'max:100'],
            'items'             => ['required', 'array', 'min:1'],
            'items.*.menu_id'   => ['required', 'exists:menus,id'],
            'items.*.quantity'  => ['required', 'integer', 'min:1', 'max:99'],
            'items.*.notes'     => ['nullable', 'string', 'max:255'],
        ], [
            'table_id.required'          => 'Pilih nomor meja terlebih dahulu.',
            'payment_method.in'          => 'Metode pembayaran tidak tersedia. Muat ulang halaman.',
            'cash_received.required_if'  => 'Masukkan uang yang diterima.',
            'items.required'             => 'Keranjang masih kosong.',
            'items.min'                  => 'Keranjang masih kosong.',
        ]);

        // Jika fitur meja OFF, abaikan table_id apa pun yang dikirim klien
        $tableId = $useTables ? $data['table_id'] : null;
        $isCash  = $data['payment_method'] === 'cash';

        $transaction = DB::transaction(function () use ($data, $tableId, $isCash) {
            $table = null;

            if ($tableId) {
                $table = RestaurantTable::lockForUpdate()->findOrFail($tableId);

                // Meja reservasi boleh dipakai (tamu reservasi datang); hanya meja terisi yang ditolak
                if ($table->status === 'occupied') {
                    throw ValidationException::withMessages(['table_id' => 'Meja sedang terisi, pilih meja lain.']);
                }
            }

            // Harga selalu diambil dari database, bukan dari input klien
            $menus = Menu::whereIn('id', collect($data['items'])->pluck('menu_id'))->get()->keyBy('id');

            $total = 0;
            $rows  = [];

            foreach ($data['items'] as $item) {
                $menu = $menus[$item['menu_id']];

                if (! $menu->is_available) {
                    throw ValidationException::withMessages(['items' => "{$menu->name} sedang habis."]);
                }

                $subtotal = $menu->price * $item['quantity'];
                $total   += $subtotal;

                $rows[] = [
                    'menu_id'  => $menu->id,
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal,
                    'notes'    => $item['notes'] ?? null,
                ];
            }

            if ($isCash && $data['cash_received'] < $total) {
                throw ValidationException::withMessages(['cash_received' => 'Uang yang diterima kurang dari total.']);
            }

            // Non-tunai: dibayar pas, tanpa kembalian
            $cashReceived = $isCash ? $data['cash_received'] : $total;

            $transaction = Transaction::create([
                'invoice_number'    => $this->generateInvoiceNumber(),
                'table_id'          => $tableId,
                'cashier_id'        => auth()->id(),
                'total_amount'      => $total,
                'cash_received'     => $cashReceived,
                'change_amount'     => $cashReceived - $total,
                'payment_method'    => $data['payment_method'],
                'payment_reference' => $isCash ? null : ($data['payment_reference'] ?? null),
                'status'            => 'paid',
            ]);

            $transaction->items()->createMany($rows);

            if ($table) {
                $table->update(['status' => 'occupied']);
            }

            return $transaction;
        });

        return response()->json([
            'redirect' => route('cashier.receipt', $transaction),
        ]);
    }

    public function receipt(Transaction $transaction)
    {
        $transaction->load(['items.menu', 'table', 'cashier']);

        return view('cashier.receipt', compact('transaction'));
    }

    // Dipanggil POS tiap beberapa detik agar peta meja selalu terbaru
    public function tables(): JsonResponse
    {
        $enabled = Setting::isActive(Setting::TABLE_MANAGEMENT);

        return response()->json([
            'enabled' => $enabled,
            'tables'  => $enabled ? $this->tableList() : [],
        ]);
    }

    // Kasir menandai atau membatalkan reservasi meja
    public function toggleReservation(RestaurantTable $table): JsonResponse
    {
        if (! Setting::isActive(Setting::TABLE_MANAGEMENT)) {
            return response()->json(['message' => 'Fitur Manajemen Meja sedang nonaktif.'], 422);
        }

        $status = DB::transaction(function () use ($table) {
            $row = RestaurantTable::lockForUpdate()->findOrFail($table->id);

            if ($row->status === 'occupied') {
                return null;
            }

            $row->update(['status' => $row->status === 'reserved' ? 'available' : 'reserved']);

            return $row->status;
        });

        if ($status === null) {
            return response()->json(['message' => 'Meja sedang terisi, status tidak bisa diubah.'], 422);
        }

        return response()->json(['status' => $status]);
    }

    // Metode pembayaran yang sedang aktif (tunai selalu ada)
    private function availableMethods(): array
    {
        $methods = ['cash' => 'Tunai'];

        if (Setting::isActive(Setting::PAYMENT_QRIS) && Setting::getValue(Setting::QRIS_IMAGE)) {
            $methods['qris'] = 'QRIS';
        }

        if (Setting::isActive(Setting::PAYMENT_DEBIT)) {
            $methods['debit'] = 'Debit';
        }

        return $methods;
    }

    private function tableList(): array
    {
        return RestaurantTable::orderByRaw('CAST(table_number AS UNSIGNED), table_number')
            ->get(['id', 'table_number', 'status'])
            ->map(fn ($t) => [
                'id'     => $t->id,
                'number' => $t->table_number,
                'status' => $t->status,
            ])
            ->all();
    }

    private function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . now()->format('Ymd') . '-';
        $count  = Transaction::whereDate('created_at', today())->lockForUpdate()->count() + 1;

        return $prefix . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}