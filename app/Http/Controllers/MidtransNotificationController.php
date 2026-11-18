<?php

namespace App\Http\Controllers;

use App\Models\MidtransPayment;
use App\Services\MidtransPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MidtransNotificationController extends Controller
{
    // Notifikasi otomatis dari Midtrans (tanpa login, tanpa CSRF)
    public function __invoke(Request $request, MidtransPaymentService $service): JsonResponse
    {
        $orderId = (string) $request->input('order_id');

        // Tanda tangan: sha512(order_id + status_code + gross_amount + server_key)
        $expected = hash('sha512',
            $orderId . $request->input('status_code') . $request->input('gross_amount') . config('midtrans.server_key')
        );

        if (! hash_equals($expected, (string) $request->input('signature_key'))) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $payment = MidtransPayment::where('order_id', $orderId)->first();

        // Status asli tetap dicek langsung ke Midtrans, bukan dari isi notifikasi
        if ($payment) {
            $service->sync($payment);
        }

        return response()->json(['message' => 'OK']);
    }
}