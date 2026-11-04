@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    @php
        $statusLabels = ['paid' => 'Antre', 'cooking' => 'Dimasak', 'done' => 'Selesai', 'cancelled' => 'Batal'];
        $methodLabels = ['cash' => 'Tunai', 'qris' => 'QRIS', 'debit' => 'Debit'];
        $rp = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');
    @endphp

    {{-- Filter & export --}}
    <form method="GET" action="{{ route('admin.dashboard') }}" class="toolbar">
        <div style="display:flex; flex-wrap:wrap; gap:.5rem; align-items:flex-end;">
            <div>
                <label for="from" class="block text-xs text-gray-500">Dari</label>
                <input id="from" type="date" name="from" value="{{ $from->toDateString() }}"
                       class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label for="to" class="block text-xs text-gray-500">Sampai</label>
                <input id="to" type="date" name="to" value="{{ $to->toDateString() }}"
                       class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            </div>
            <button class="rounded-lg bg-gray-800 px-4 py-2 text-sm text-white">Terapkan</button>
        </div>

        <div style="display:flex; flex-wrap:wrap; gap:.5rem;">
            <a href="{{ route('admin.dashboard', ['from' => today()->toDateString(), 'to' => today()->toDateString()]) }}"
               class="rounded-lg border bg-white px-3 py-2 text-sm hover:bg-gray-50">Hari ini</a>
            <a href="{{ route('admin.dashboard', ['from' => today()->subDays(6)->toDateString(), 'to' => today()->toDateString()]) }}"
               class="rounded-lg border bg-white px-3 py-2 text-sm hover:bg-gray-50">7 hari terakhir</a>
            <a href="{{ route('admin.dashboard', ['from' => today()->startOfMonth()->toDateString(), 'to' => today()->toDateString()]) }}"
               class="rounded-lg border bg-white px-3 py-2 text-sm hover:bg-gray-50">Bulan ini</a>
            <a href="{{ route('admin.dashboard.export', ['from' => $from->toDateString(), 'to' => $to->toDateString()]) }}"
               style="background:#16a34a; color:#fff; border-radius:.5rem; padding:.5rem 1rem; font-size:.875rem; font-weight:600; text-decoration:none;">
                Export Excel
            </a>
        </div>
    </form>

    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-100 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>
    @endif

    {{-- Ringkasan --}}
    <div class="stat-grid">
        <div class="rounded-xl bg-white p-6 shadow">
            <div class="text-sm text-gray-500">Total Pendapatan</div>
            <div class="mt-1 text-3xl font-bold">{{ $rp($summary['revenue']) }}</div>
        </div>
        <div class="rounded-xl bg-white p-6 shadow">
            <div class="text-sm text-gray-500">Jumlah Transaksi</div>
            <div class="mt-1 text-3xl font-bold">{{ $summary['count'] }}</div>
        </div>
        <div class="rounded-xl bg-white p-6 shadow">
            <div class="text-sm text-gray-500">Rata-rata per Transaksi</div>
            <div class="mt-1 text-3xl font-bold">{{ $rp($summary['average']) }}</div>
        </div>
    </div>

    {{-- Pendapatan per metode --}}
    @if ($methods->isNotEmpty())
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(14rem, 1fr)); gap:1rem; margin-top:1rem;">
            @foreach ($methods as $m)
                <div class="rounded-xl bg-white p-4 shadow">
                    <div class="text-sm text-gray-500">
                        {{ $methodLabels[$m->payment_method] ?? $m->payment_method }} &middot; {{ $m->trx }} transaksi
                    </div>
                    <div class="mt-1 text-xl font-bold">{{ $rp($m->revenue) }}</div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Per hari & menu terlaris --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(20rem, 1fr)); gap:1rem; margin-top:1rem;">
        <div class="overflow-x-auto rounded-xl bg-white shadow">
            <div class="px-4 py-3 font-semibold">Pendapatan per Hari</div>
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left text-gray-600">
                    <tr>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Transaksi</th>
                        <th class="px-4 py-3 text-right">Pendapatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($daily as $row)
                        <tr>
                            <td class="px-4 py-3">{{ \Illuminate\Support\Carbon::parse($row->day)->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">{{ $row->trx }}</td>
                            <td class="px-4 py-3 text-right font-medium">{{ $rp($row->revenue) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-4 py-8 text-center text-gray-500">Belum ada transaksi pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="overflow-x-auto rounded-xl bg-white shadow">
            <div class="px-4 py-3 font-semibold">Menu Terlaris</div>
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left text-gray-600">
                    <tr>
                        <th class="px-4 py-3">Menu</th>
                        <th class="px-4 py-3">Terjual</th>
                        <th class="px-4 py-3 text-right">Nilai</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($topMenus as $menu)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $menu->name }}</td>
                            <td class="px-4 py-3">{{ $menu->qty }}</td>
                            <td class="px-4 py-3 text-right">{{ $rp($menu->revenue) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-4 py-8 text-center text-gray-500">Belum ada data penjualan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Daftar transaksi --}}
    <div class="mt-4 overflow-x-auto rounded-xl bg-white shadow">
        <div class="px-4 py-3 font-semibold">Daftar Transaksi</div>
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-600">
                <tr>
                    <th class="px-4 py-3">Invoice</th>
                    <th class="px-4 py-3">Waktu</th>
                    <th class="px-4 py-3">Kasir</th>
                    <th class="px-4 py-3">Meja</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Metode</th>
                    <th class="px-4 py-3 text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($transactions as $trx)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $trx->invoice_number }}</td>
                        <td class="px-4 py-3">{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">{{ $trx->cashier->name }}</td>
                        <td class="px-4 py-3">{{ $trx->table?->table_number ?? 'Takeaway' }}</td>
                        <td class="px-4 py-3">{{ $statusLabels[$trx->status] ?? $trx->status }}</td>
                        <td class="px-4 py-3">{{ $methodLabels[$trx->payment_method] ?? $trx->payment_method }}</td>
                        <td class="px-4 py-3 text-right font-medium">{{ $rp($trx->total_amount) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">Belum ada transaksi pada periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $transactions->links() }}</div>
@endsection