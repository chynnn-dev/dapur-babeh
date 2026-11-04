<?php

namespace App\Support;

use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class SalesReport
{
    public function __construct(
        public readonly Carbon $from,
        public readonly Carbon $to,
    ) {
    }

    public static function make(?string $from, ?string $to): self
    {
        return new self(
            Carbon::parse($from ?: today())->startOfDay(),
            Carbon::parse($to ?: today())->endOfDay(),
        );
    }

    // Transaksi yang dihitung: dalam periode dan tidak dibatalkan
    public function transactionsQuery(): Builder
    {
        return Transaction::where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$this->from, $this->to]);
    }

    public function summary(): array
    {
        $row = $this->transactionsQuery()
            ->selectRaw('COUNT(*) as total_transactions, COALESCE(SUM(total_amount), 0) as revenue')
            ->first();

        $count   = (int) $row->total_transactions;
        $revenue = (int) $row->revenue;

        return [
            'count'   => $count,
            'revenue' => $revenue,
            'average' => $count > 0 ? intdiv($revenue, $count) : 0,
        ];
    }

    public function daily(): Collection
    {
        return $this->transactionsQuery()
            ->selectRaw('DATE(created_at) as day, COUNT(*) as trx, SUM(total_amount) as revenue')
            ->groupBy('day')
            ->orderByDesc('day')
            ->get();
    }

    public function byMethod(): Collection
{
    return $this->transactionsQuery()
        ->selectRaw('payment_method, COUNT(*) as trx, SUM(total_amount) as revenue')
        ->groupBy('payment_method')
        ->orderByDesc('revenue')
        ->get();
}

    public function menus(?int $limit = null): Collection
    {
        $query = TransactionItem::query()
            ->join('transactions', 'transactions.id', '=', 'transaction_items.transaction_id')
            ->join('menus', 'menus.id', '=', 'transaction_items.menu_id')
            ->where('transactions.status', '!=', 'cancelled')
            ->whereBetween('transactions.created_at', [$this->from, $this->to])
            ->selectRaw('menus.name, SUM(transaction_items.quantity) as qty, SUM(transaction_items.subtotal) as revenue')
            ->groupBy('menus.id', 'menus.name')
            ->orderByDesc('qty');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }
}