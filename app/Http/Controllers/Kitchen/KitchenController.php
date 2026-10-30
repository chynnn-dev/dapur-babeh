<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use App\Models\RestaurantTable;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class KitchenController extends Controller
{
    public function index()
    {
        return view('kitchen.index');
    }

    // Dipanggil layar dapur setiap beberapa detik
    public function orders(): JsonResponse
    {
        $orders = Transaction::with(['items.menu', 'table'])
            ->whereIn('status', ['paid', 'cooking'])
            ->oldest()
            ->get()
            ->map(fn ($t) => [
                'id'      => $t->id,
                'invoice' => $t->invoice_number,
                'table'   => $t->table?->table_number,
                'status'  => $t->status,
                'minutes' => max(0, (int) floor((now()->timestamp - $t->created_at->timestamp) / 60)),
                'items'   => $t->items->map(fn ($i) => [
                    'name'     => $i->menu->name,
                    'quantity' => $i->quantity,
                    'notes'    => $i->notes,
                ])->values(),
            ]);

        return response()->json(['orders' => $orders]);
    }

    public function updateStatus(Request $request, Transaction $transaction): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'in:cooking,done'],
        ]);

        // Alur satu arah: paid -> cooking -> done
        $allowed = ['paid' => 'cooking', 'cooking' => 'done'];

        DB::transaction(function () use ($request, $transaction, $allowed) {
            $order = Transaction::lockForUpdate()->findOrFail($transaction->id);

            if (($allowed[$order->status] ?? null) !== $request->status) {
                throw ValidationException::withMessages([
                    'status' => 'Status pesanan sudah berubah. Layar akan dimuat ulang.',
                ]);
            }

            $order->update(['status' => $request->status]);

            if ($request->status === 'done' && $order->table_id) {
                RestaurantTable::where('id', $order->table_id)->update(['status' => 'available']);
            }
        });

        return response()->json(['ok' => true]);
    }
}