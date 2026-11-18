<?php

namespace App\Http\Controllers\Waiter;

use App\Http\Controllers\Controller;
use App\Models\RestaurantTable;
use App\Models\Setting;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WaiterController extends Controller
{
    // Pesanan yang belum selesai diantar
    private const OPEN = ['paid', 'cooking', 'ready'];

    public function index()
    {
        return view('waiter.index');
    }

    // Dipanggil layar pelayan tiap beberapa detik
    public function data(): JsonResponse
    {
        $ready = Transaction::with(['items.menu', 'table'])
            ->where('status', 'ready')
            ->oldest('updated_at')
            ->get()
            ->map(fn ($t) => [
                'id'      => $t->id,
                'invoice' => $t->invoice_number,
                'table'   => $t->table?->table_number,
                'minutes' => max(0, (int) floor((now()->timestamp - $t->updated_at->timestamp) / 60)),
                'items'   => $t->items->map(fn ($i) => [
                    'name'     => $i->menu->name,
                    'quantity' => $i->quantity,
                    'notes'    => $i->notes,
                ])->values(),
            ]);

        // Jumlah pesanan per meja yang belum diantar
        $open = Transaction::whereIn('status', self::OPEN)
            ->whereNotNull('table_id')
            ->selectRaw('table_id, COUNT(*) as total')
            ->groupBy('table_id')
            ->pluck('total', 'table_id');

        $enabled = Setting::isActive(Setting::TABLE_MANAGEMENT);

        $tables = $enabled
            ? RestaurantTable::orderByRaw('CAST(table_number AS UNSIGNED), table_number')
                ->get()
                ->map(fn ($t) => [
                    'id'     => $t->id,
                    'number' => $t->table_number,
                    'status' => $t->status,
                    'open'   => (int) ($open[$t->id] ?? 0),
                ])
                ->values()
            : [];

        return response()->json([
            'ready'          => $ready,
            'tables'         => $tables,
            'tables_enabled' => $enabled,
        ]);
    }

    // Pelayan menandai pesanan sudah diantar (atau diserahkan untuk takeaway)
    public function serve(Transaction $transaction): JsonResponse
    {
        DB::transaction(function () use ($transaction) {
            $order = Transaction::lockForUpdate()->findOrFail($transaction->id);

            if ($order->status !== 'ready') {
                throw ValidationException::withMessages([
                    'status' => 'Status pesanan sudah berubah. Layar akan dimuat ulang.',
                ]);
            }

            $order->update(['status' => 'served']);
        });

        return response()->json(['ok' => true]);
    }

    // Pelayan mengosongkan meja setelah pelanggan pergi
    public function clearTable(RestaurantTable $table): JsonResponse
    {
        DB::transaction(function () use ($table) {
            $row = RestaurantTable::lockForUpdate()->findOrFail($table->id);

            if ($row->status !== 'occupied') {
                throw ValidationException::withMessages([
                    'table' => 'Meja ini tidak sedang terisi.',
                ]);
            }

            $open = Transaction::where('table_id', $row->id)->whereIn('status', self::OPEN)->count();

            if ($open > 0) {
                throw ValidationException::withMessages([
                    'table' => "Masih ada {$open} pesanan di meja ini yang belum diantar.",
                ]);
            }

            $row->update(['status' => 'available']);
        });

        return response()->json(['ok' => true]);
    }
}