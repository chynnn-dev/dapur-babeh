<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to'   => ['nullable', 'date', 'after_or_equal:from'],
        ], [
            'to.after_or_equal' => 'Tanggal akhir tidak boleh sebelum tanggal awal.',
        ]);

        $from = Carbon::parse($request->input('from', today()))->startOfDay();
        $to   = Carbon::parse($request->input('to', today()))->endOfDay();

        $base = Transaction::where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$from, $to]);

        $summary = (clone $base)
            ->selectRaw('COUNT(*) as total_transactions, COALESCE(SUM(total_amount), 0) as revenue')
            ->first();

        $count   = (int) $summary->total_transactions;
        $revenue = (int) $summary->revenue;
        $average = $count > 0 ? intdiv($revenue, $count) : 0;

        $daily = (clone $base)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as trx, SUM(total_amount) as revenue')
            ->groupBy('day')
            ->orderByDesc('day')
            ->get();

        $topMenus = TransactionItem::query()
            ->join('transactions', 'transactions.id', '=', 'transaction_items.transaction_id')
            ->join('menus', 'menus.id', '=', 'transaction_items.menu_id')
            ->where('transactions.status', '!=', 'cancelled')
            ->whereBetween('transactions.created_at', [$from, $to])
            ->selectRaw('menus.name, SUM(transaction_items.quantity) as qty, SUM(transaction_items.subtotal) as revenue')
            ->groupBy('menus.id', 'menus.name')
            ->orderByDesc('qty')
            ->limit(10)
            ->get();

        $transactions = (clone $base)
            ->with(['cashier', 'table'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.reports.index', compact(
            'from', 'to', 'count', 'revenue', 'average', 'daily', 'topMenus', 'transactions'
        ));
    }
}