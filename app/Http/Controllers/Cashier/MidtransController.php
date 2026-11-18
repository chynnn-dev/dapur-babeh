<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\MidtransPayment;
use App\Models\Setting;
use App\Services\MidtransPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MidtransController extends Controller
{
    public function __construct(private MidtransPaymentService $service)
    {
    }

    // Buat pembayaran & kembalikan Snap token
    public function create(Request $request): JsonResponse
    {
        abort_unless($this->service->enabled(), 404);

        $useTables = Setting::isActive(Setting::TABLE_MANAGEMENT);

        $data = $request->validate([
            'table_id'         => [$useTables ? 'required' : 'nullable', 'exists:tables,id'],
            'items'            => ['required', 'array', 'min:1'],
            'items.*.menu_id'  => ['required', 'exists:menus,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'items.*.notes'    => ['nullable', 'string', 'max:255'],
        ], [
            'table_id.required' => 'Pilih nomor meja terlebih dahulu.',
            'items.required'    => 'Keranjang masih kosong.',
            'items.min'         => 'Keranjang masih kosong.',
        ]);

        // Jika fitur meja OFF, abaikan table_id apa pun yang dikirim klien
        $tableId = $useTables ? (int) $data['table_id'] : null;

        $payment = $this->service->create($data, $tableId, auth()->id());

        return response()->json([
            'order_id' => $payment->order_id,
            'token'    => $payment->snap_token,
            'total'    => $payment->total_amount,
        ]);
    }

    // Dipanggil kasir berkala untuk mengecek apakah pelanggan sudah membayar
    public function status(string $orderId): JsonResponse
    {
        $payment = $this->own($orderId);
        $payment = $this->service->sync($payment);

        return $this->payload($payment);
    }

    public function cancel(string $orderId): JsonResponse
    {
        $payment = $this->own($orderId);
        $payment = $this->service->cancel($payment);

        return $this->payload($payment);
    }

    private function own(string $orderId): MidtransPayment
    {
        return MidtransPayment::where('order_id', $orderId)
            ->where('cashier_id', auth()->id())
            ->firstOrFail();
    }

    private function payload(MidtransPayment $payment): JsonResponse
    {
        $out = ['status' => $payment->status];

        if ($payment->status === 'paid' && $payment->transaction_id) {
            $out['redirect'] = route('cashier.receipt', $payment->transaction_id);
        }

        return response()->json($out);
    }
}