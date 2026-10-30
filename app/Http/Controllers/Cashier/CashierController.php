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

        $tables = $useTables
            ? RestaurantTable::where('status', 'available')
                ->orderByRaw('CAST(table_number AS UNSIGNED), table_number')
                ->get(['id', 'table_number'])
            : collect();

        return view('cashier.pos', compact('useTables', 'categories', 'menus', 'tables'));
    }

    public function store(Request $request): JsonResponse
    {
        $useTables = Setting::isActive(Setting::TABLE_MANAGEMENT);

        $data = $request->validate([
            'table_id'         => [$useTables ? 'required' : 'nullable', 'exists:tables,id'],
            'cash_received'    => ['required', 'integer', 'min:0'],
            'items'            => ['required', 'array', 'min:1'],
            'items.*.menu_id'  => ['required', 'exists:menus,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'items.*.notes'    => ['nullable', 'string', 'max:255'],
        ], [
            'table_id.required'     => 'Pilih nomor meja terlebih dahulu.',
            'cash_received.required' => 'Masukkan uang yang diterima.',
            'items.required'        => 'Keranjang masih kosong.',
            'items.min'             => 'Keranjang masih kosong.',
        ]);

        // Jika fitur meja OFF, abaikan table_id apa pun yang dikirim klien
        $tableId = $useTables ? $data['table_id'] : null;

        $transaction = DB::transaction(function () use ($data, $tableId) {
            $table = null;

            if ($tableId) {
                $table = RestaurantTable::lockForUpdate()->findOrFail($tableId);

                if ($table->status !== 'available') {
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

            if ($data['cash_received'] < $total) {
                throw ValidationException::withMessages(['cash_received' => 'Uang yang diterima kurang dari total.']);
            }

            $transaction = Transaction::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'table_id'       => $tableId,
                'cashier_id'     => auth()->id(),
                'total_amount'   => $total,
                'cash_received'  => $data['cash_received'],
                'change_amount'  => $data['cash_received'] - $total,
                'payment_method' => 'cash',
                'status'         => 'paid',
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

    private function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . now()->format('Ymd') . '-';
        $count  = Transaction::whereDate('created_at', today())->lockForUpdate()->count() + 1;

        return $prefix . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}