@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    @php
        $statusLabels = ['paid' => 'Antre', 'cooking' => 'Dimasak', 'done' => 'Selesai', 'cancelled' => 'Batal'];
        $methodLabels = ['cash' => 'Tunai', 'qris' => 'QRIS', 'debit' => 'Debit'];
        $rp = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');

        $maxDaily = max(1, (int) collect($daily)->max('revenue'));
        $maxQty   = max(1, (int) collect($topMenus)->max('qty'));
        $totalRev = max(1, (int) collect($methods)->sum('revenue'));

        $today = today()->toDateString();
        $ranges = [
            'Hari ini'       => [$today, $today],
            '7 hari terakhir' => [today()->subDays(6)->toDateString(), $today],
            'Bulan ini'      => [today()->startOfMonth()->toDateString(), $today],
        ];
    @endphp

    <style>
        .db { --g: #0f5f46; --gd: #0b4a37; --o: #f58220; --line: #ece6d9; --muted: #6b7280; }
        .db * { box-sizing: border-box; }
        .db-card { background: #fff; border-radius: .9rem; box-shadow: 0 1px 3px rgba(15,95,70,.08), 0 6px 18px rgba(15,95,70,.05); }
        .db-section { margin-top: 1.1rem; }

        /* Filter */
        .db-bar { display: flex; flex-wrap: wrap; gap: .75rem; align-items: flex-end; justify-content: space-between; padding: 1rem 1.1rem; }
        .db-dates { display: flex; flex-wrap: wrap; gap: .6rem; align-items: flex-end; }
        .db-dates label { display: block; font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: var(--muted); margin-bottom: .2rem; }
        .db-input { border: 1px solid #d9cfbd; border-radius: .5rem; padding: .45rem .65rem; font-size: .85rem; background: #fbf9f4; }
        .db-input:focus { outline: 2px solid var(--o); border-color: var(--o); background: #fff; }
        .db-btn { border: 0; border-radius: .5rem; padding: .5rem 1rem; font-size: .85rem; font-weight: 700; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: .4rem; }
        .db-btn-primary { background: var(--g); color: #fff; }
        .db-btn-primary:hover { background: var(--gd); }
        .db-btn-export { background: var(--o); color: #fff; }
        .db-btn-export:hover { background: #d96d10; }
        .db-chips { display: flex; flex-wrap: wrap; gap: .45rem; align-items: center; }
        .db-chip { padding: .4rem .85rem; border-radius: 99px; font-size: .8rem; font-weight: 700; color: var(--g); background: #e9f3ee; text-decoration: none; }
        .db-chip:hover { background: #d3e8de; }
        .db-chip.on { background: var(--g); color: #fff; }

        /* Statistik */
        .db-stats { display: grid; gap: 1rem; grid-template-columns: repeat(auto-fit, minmax(15rem, 1fr)); }
        .db-stat { display: flex; align-items: center; gap: 1rem; padding: 1.1rem 1.2rem; position: relative; overflow: hidden; }
        .db-stat::before { content: ""; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: var(--c, var(--g)); }
        .db-ico { width: 2.9rem; height: 2.9rem; border-radius: .8rem; display: grid; place-items: center; flex-shrink: 0; background: var(--bg, #e9f3ee); color: var(--c, var(--g)); }
        .db-stat-label { font-size: .78rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: var(--muted); }
        .db-stat-value { font-size: 1.6rem; font-weight: 800; line-height: 1.2; color: #111827; }

        /* Kartu daftar */
        .db-grid { display: grid; gap: 1rem; grid-template-columns: repeat(auto-fit, minmax(22rem, 1fr)); }
        .db-head { display: flex; justify-content: space-between; align-items: center; padding: .95rem 1.1rem; border-bottom: 1px solid var(--line); }
        .db-head h3 { margin: 0; font-size: .95rem; font-weight: 800; color: var(--g); }
        .db-head small { color: var(--muted); font-size: .75rem; }
        .db-list { max-height: 21rem; overflow-y: auto; padding: .4rem 1.1rem .8rem; }
        .db-row { display: grid; grid-template-columns: 5.2rem 1fr auto; gap: .75rem; align-items: center; padding: .5rem 0; border-bottom: 1px dashed var(--line); font-size: .85rem; }
        .db-row:last-child { border-bottom: 0; }
        .db-row.menu { grid-template-columns: 1.6rem 1fr auto; }
        .db-bar-track { height: .5rem; background: #f0ebe0; border-radius: 99px; overflow: hidden; }
        .db-bar-fill { height: 100%; border-radius: 99px; background: linear-gradient(90deg, var(--g), #2f9a74); }
        .db-bar-fill.o { background: linear-gradient(90deg, var(--o), #f9a55c); }
        .db-amount { font-weight: 700; white-space: nowrap; }
        .db-sub { font-size: .72rem; color: var(--muted); }
        .db-rank { width: 1.5rem; height: 1.5rem; border-radius: 50%; display: grid; place-items: center; font-size: .72rem; font-weight: 800; background: #f0ebe0; color: var(--g); }
        .db-rank.top { background: var(--o); color: #fff; }

        /* Metode pembayaran */
        .db-methods { display: grid; gap: 1rem; grid-template-columns: repeat(auto-fit, minmax(14rem, 1fr)); }
        .db-method { padding: 1rem 1.1rem; }
        .db-method-top { display: flex; justify-content: space-between; align-items: baseline; font-size: .82rem; color: var(--muted); font-weight: 700; }
        .db-method-val { font-size: 1.25rem; font-weight: 800; margin: .2rem 0 .55rem; color: #111827; }

        /* Tabel */
        .db-table-wrap { overflow-x: auto; }
        .db-table { width: 100%; border-collapse: collapse; font-size: .85rem; }
        .db-table th { text-align: left; padding: .7rem 1rem; font-size: .7rem; text-transform: uppercase; letter-spacing: .06em; color: var(--muted); background: #faf8f3; white-space: nowrap; }
        .db-table td { padding: .75rem 1rem; border-top: 1px solid var(--line); white-space: nowrap; }
        .db-table tbody tr:hover { background: #fbf9f4; }
        .db-table .r { text-align: right; }
        .db-inv { font-weight: 800; color: var(--g); }
        .db-badge { display: inline-block; padding: .15rem .65rem; border-radius: 99px; font-size: .72rem; font-weight: 700; }
        .st-paid { background: #fff1de; color: #9a5a00; }
        .st-cooking { background: #e0edff; color: #1d4ed8; }
        .st-done { background: #e3f4ea; color: #0b4a37; }
        .st-cancelled { background: #fde2e2; color: #a12626; }
        .db-empty { text-align: center; color: var(--muted); padding: 2rem 1rem !important; }
        .db-empty-box { text-align: center; color: var(--muted); padding: 1.8rem 1rem; font-size: .85rem; }
        .db-pager { margin-top: 1rem; }
    </style>

    <div class="db">

        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-100 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>
        @endif

        {{-- Filter & export --}}
        <form method="GET" action="{{ route('admin.dashboard') }}" class="db-card db-bar">
            <div class="db-dates">
                <div>
                    <label for="from">Dari</label>
                    <input id="from" type="date" name="from" value="{{ $from->toDateString() }}" class="db-input">
                </div>
                <div>
                    <label for="to">Sampai</label>
                    <input id="to" type="date" name="to" value="{{ $to->toDateString() }}" class="db-input">
                </div>
                <button class="db-btn db-btn-primary">Terapkan</button>
            </div>

            <div class="db-chips">
                @foreach ($ranges as $label => [$f, $t])
                    <a href="{{ route('admin.dashboard', ['from' => $f, 'to' => $t]) }}"
                       class="db-chip {{ $from->toDateString() === $f && $to->toDateString() === $t ? 'on' : '' }}">{{ $label }}</a>
                @endforeach
                <a href="{{ route('admin.dashboard.export', ['from' => $from->toDateString(), 'to' => $to->toDateString()]) }}"
                   class="db-btn db-btn-export">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12m0 0-4-4m4 4 4-4M5 21h14"/></svg>
                    Export Excel
                </a>
            </div>
        </form>

        {{-- Ringkasan --}}
        <div class="db-stats db-section">
            <div class="db-card db-stat">
                <div class="db-ico">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7h18v12H3zM3 7l3-4h12l3 4M16 13h2"/></svg>
                </div>
                <div>
                    <div class="db-stat-label">Total Pendapatan</div>
                    <div class="db-stat-value">{{ $rp($summary['revenue']) }}</div>
                </div>
            </div>
            <div class="db-card db-stat" style="--c:#f58220; --bg:#fff1de;">
                <div class="db-ico">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h12v18l-3-2-3 2-3-2-3 2zM9 8h6M9 12h6"/></svg>
                </div>
                <div>
                    <div class="db-stat-label">Jumlah Transaksi</div>
                    <div class="db-stat-value">{{ number_format($summary['count'], 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="db-card db-stat" style="--c:#2563eb; --bg:#e0edff;">
                <div class="db-ico">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20V10m6 10V4m6 16v-7m4 7H2"/></svg>
                </div>
                <div>
                    <div class="db-stat-label">Rata-rata / Transaksi</div>
                    <div class="db-stat-value">{{ $rp($summary['average']) }}</div>
                </div>
            </div>
        </div>

        {{-- Pendapatan per metode --}}
        @if ($methods->isNotEmpty())
            <div class="db-methods db-section">
                @foreach ($methods as $m)
                    <div class="db-card db-method">
                        <div class="db-method-top">
                            <span>{{ $methodLabels[$m->payment_method] ?? $m->payment_method }}</span>
                            <span>{{ $m->trx }} transaksi</span>
                        </div>
                        <div class="db-method-val">{{ $rp($m->revenue) }}</div>
                        <div class="db-bar-track"><div class="db-bar-fill o" style="width: {{ round($m->revenue / $totalRev * 100) }}%"></div></div>
                        <div class="db-sub" style="margin-top:.3rem">{{ round($m->revenue / $totalRev * 100) }}% dari total</div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Per hari & menu terlaris --}}
        <div class="db-grid db-section">
            <div class="db-card">
                <div class="db-head">
                    <h3>Pendapatan per Hari</h3>
                    <small>{{ $from->format('d/m') }} &ndash; {{ $to->format('d/m/Y') }}</small>
                </div>
                @if (collect($daily)->isNotEmpty())
                    <div class="db-list">
                        @foreach ($daily as $row)
                            <div class="db-row">
                                <div>
                                    <div style="font-weight:700">{{ \Illuminate\Support\Carbon::parse($row->day)->format('d/m/Y') }}</div>
                                    <div class="db-sub">{{ $row->trx }} transaksi</div>
                                </div>
                                <div class="db-bar-track"><div class="db-bar-fill" style="width: {{ max(3, round($row->revenue / $maxDaily * 100)) }}%"></div></div>
                                <div class="db-amount">{{ $rp($row->revenue) }}</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="db-empty-box">Belum ada transaksi pada periode ini.</div>
                @endif
            </div>

            <div class="db-card">
                <div class="db-head">
                    <h3>Menu Terlaris</h3>
                    <small>Berdasarkan jumlah terjual</small>
                </div>
                @if (collect($topMenus)->isNotEmpty())
                    <div class="db-list">
                        @foreach ($topMenus as $menu)
                            <div class="db-row menu">
                                <div class="db-rank {{ $loop->iteration <= 3 ? 'top' : '' }}">{{ $loop->iteration }}</div>
                                <div>
                                    <div style="font-weight:700">{{ $menu->name }}</div>
                                    <div class="db-bar-track" style="margin-top:.3rem"><div class="db-bar-fill o" style="width: {{ max(3, round($menu->qty / $maxQty * 100)) }}%"></div></div>
                                </div>
                                <div style="text-align:right">
                                    <div class="db-amount">{{ $menu->qty }} terjual</div>
                                    <div class="db-sub">{{ $rp($menu->revenue) }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="db-empty-box">Belum ada data penjualan.</div>
                @endif
            </div>
        </div>

        {{-- Daftar transaksi --}}
        <div class="db-card db-section">
            <div class="db-head">
                <h3>Daftar Transaksi</h3>
                <small>{{ $transactions->total() }} transaksi</small>
            </div>
            <div class="db-table-wrap">
                <table class="db-table">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Waktu</th>
                            <th>Kasir</th>
                            <th>Meja</th>
                            <th>Status</th>
                            <th>Metode</th>
                            <th class="r">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $trx)
                            <tr>
                                <td class="db-inv">{{ $trx->invoice_number }}</td>
                                <td>{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $trx->cashier->name }}</td>
                                <td>{{ $trx->table?->table_number ?? 'Takeaway' }}</td>
                                <td><span class="db-badge st-{{ $trx->status }}">{{ $statusLabels[$trx->status] ?? $trx->status }}</span></td>
                                <td>{{ $methodLabels[$trx->payment_method] ?? $trx->payment_method }}</td>
                                <td class="r" style="font-weight:700">{{ $rp($trx->total_amount) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="db-empty">Belum ada transaksi pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="db-pager">{{ $transactions->links() }}</div>
    </div>
@endsection