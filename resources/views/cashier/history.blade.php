@extends('layouts.cashier')

@section('title', 'Riwayat Pembayaran')

@section('content')
<style>
    .rh { max-width: 62rem; margin: 0 auto; padding: 1rem; color: #1f2937; }
    .rh * { box-sizing: border-box; }
    .rh-top { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: .75rem; margin-bottom: 1rem; padding: .8rem 1rem; background: #fff; border-radius: .9rem; box-shadow: 0 2px 10px rgba(0,0,0,.12); }
    .rh-top h2 { margin: 0; font-size: 1.35rem; font-weight: 800; color: #0f5f46; }
    .rh-back { padding: .5rem 1rem; border-radius: .5rem; background: #0f5f46; color: #fff; font-weight: 700; font-size: .85rem; text-decoration: none; }
    .rh-card { background: #fff; border-radius: .9rem; box-shadow: 0 2px 10px rgba(0,0,0,.12); }

    .rh-bar { display: flex; flex-wrap: wrap; gap: .75rem; align-items: center; justify-content: space-between; padding: .8rem 1rem; margin-bottom: 1rem; }
    .rh-date { display: flex; gap: .4rem; align-items: center; }
    .rh-date input { border: 1px solid #d9cfbd; border-radius: .5rem; padding: .4rem .6rem; font: inherit; }
    .rh-date button { border: 0; border-radius: .5rem; padding: .45rem .9rem; background: #0f5f46; color: #fff; font-weight: 700; cursor: pointer; }
    .rh-chips { display: flex; flex-wrap: wrap; gap: .4rem; }
    .rh-chip { padding: .35rem .9rem; border-radius: 99px; border: 2px solid #0f5f46; color: #0f5f46; font-weight: 800; font-size: .8rem; text-decoration: none; }
    .rh-chip.on, .rh-chip:hover { background: #0f5f46; color: #fff; }

    .rh-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(11rem, 1fr)); gap: .75rem; margin-bottom: 1rem; }
    .rh-stat { padding: .9rem 1rem; border-left: 5px solid var(--c); }
    .rh-stat span { display: block; font-size: .72rem; font-weight: 800; text-transform: uppercase; letter-spacing: .05em; color: #6b7280; }
    .rh-stat b { font-size: 1.35rem; font-weight: 800; }

    .rh-wrap { overflow-x: auto; }
    .rh-table { width: 100%; border-collapse: collapse; font-size: .88rem; }
    .rh-table th { text-align: left; padding: .7rem 1rem; font-size: .7rem; text-transform: uppercase; letter-spacing: .06em; color: #6b7280; background: #faf8f3; white-space: nowrap; }
    .rh-table td { padding: .7rem 1rem; border-top: 1px solid #ece6d9; vertical-align: middle; }
    .rh-table .r { text-align: right; }
    .rh-no { font-weight: 800; color: #0f5f46; word-break: break-all; }
    .rh-badge-s { display: inline-block; padding: .15rem .7rem; border-radius: 99px; font-size: .72rem; font-weight: 800; white-space: nowrap; }
    .g-success { background: #e3f4ea; color: #0b4a37; }
    .g-failed  { background: #fde2e2; color: #a12626; }
    .g-pending { background: #fff1de; color: #9a5a00; }
    .rh-act { display: inline-block; padding: .3rem .75rem; border-radius: .45rem; border: 1px solid #cfe3d9; background: #fff; color: #0f5f46; font: inherit; font-size: .78rem; font-weight: 800; cursor: pointer; text-decoration: none; }
    .rh-act.danger { border-color: #f3c1c1; color: #b91c1c; }
    .rh-empty { text-align: center; color: #6b7280; padding: 2rem 1rem !important; }
</style>

<div class="rh">
    <div class="rh-top">
        <h2>Riwayat Pembayaran</h2>
        <a href="{{ route('cashier.pos') }}" class="rh-back">&larr; Kembali ke POS</a>
    </div>

    <div class="rh-card rh-bar">
        <form method="GET" class="rh-date">
            <input type="date" name="date" value="{{ $date }}">
            <input type="hidden" name="filter" value="{{ $filter }}">
            <button>Tampilkan</button>
        </form>

        <div class="rh-chips">
            @foreach (['all' => 'Semua', 'success' => 'Berhasil', 'failed' => 'Gagal / Batal', 'pending' => 'Menunggu'] as $key => $label)
                <a href="{{ route('cashier.history', ['date' => $date, 'filter' => $key]) }}"
                   class="rh-chip {{ $filter === $key ? 'on' : '' }}">{{ $label }}</a>
            @endforeach
        </div>
    </div>

    <div class="rh-stats">
        <div class="rh-card rh-stat" style="--c:#22a06b">
            <span>Berhasil</span>
            <b>{{ $summary['success_count'] }}</b>
        </div>
        <div class="rh-card rh-stat" style="--c:#0f5f46">
            <span>Total Berhasil</span>
            <b>Rp {{ number_format($summary['success_total'], 0, ',', '.') }}</b>
        </div>
        <div class="rh-card rh-stat" style="--c:#dc2626">
            <span>Gagal / Batal</span>
            <b>{{ $summary['failed'] }}</b>
        </div>
        <div class="rh-card rh-stat" style="--c:#f58220">
            <span>Menunggu</span>
            <b>{{ $summary['pending'] }}</b>
        </div>
    </div>

    <div class="rh-card rh-wrap">
        <table class="rh-table">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>No. Invoice / Order</th>
                    <th>Meja</th>
                    <th>Metode</th>
                    <th>Kasir</th>
                    <th class="r">Total</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr @if ($row['group'] === 'pending') data-pending="{{ $row['order_id'] }}" @endif>
                        <td>{{ $row['time']->format('H:i') }}</td>
                        <td class="rh-no">{{ $row['number'] }}</td>
                        <td>{{ $row['table'] }}</td>
                        <td>{{ $row['method'] }}</td>
                        <td>{{ $row['cashier'] ?? '-' }}</td>
                        <td class="r"><b>Rp {{ number_format($row['total'], 0, ',', '.') }}</b></td>
                        <td><span class="rh-badge-s g-{{ $row['group'] }}">{{ $row['label'] }}</span></td>
                        <td>
                            @if ($row['receipt'])
                                <a href="{{ $row['receipt'] }}" class="rh-act">Lihat Struk</a>
                            @elseif ($row['order_id'])
                                <button type="button" class="rh-act" data-check>Cek Status</button>
                                <button type="button" class="rh-act danger" data-cancel="{{ $row['order_id'] }}">Batalkan</button>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="rh-empty">Belum ada data pada tanggal ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const base = @json(url('/cashier/midtrans/payments'));
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const pending = [...document.querySelectorAll('[data-pending]')].map((tr) => tr.dataset.pending);

    // Periksa status pembayaran yang masih menunggu; muat ulang halaman jika ada yang berubah
    async function sweep() {
        let changed = false;

        for (const id of pending) {
            try {
                const res = await fetch(`${base}/${id}/status`, { headers: { 'Accept': 'application/json' } });
                if (!res.ok) continue;
                const data = await res.json();
                if (data.status !== 'pending') changed = true;
            } catch (err) { /* coba lagi nanti */ }
        }

        if (changed) window.location.reload();
    }

    if (pending.length) {
        sweep();
        setInterval(sweep, 7000);
    }

    document.querySelectorAll('[data-check]').forEach((btn) =>
        btn.addEventListener('click', async () => {
            btn.disabled = true;
            await sweep();
            btn.disabled = false;
        }));

    document.querySelectorAll('[data-cancel]').forEach((btn) =>
        btn.addEventListener('click', async () => {
            if (!confirm('Batalkan pembayaran ini?')) return;
            btn.disabled = true;

            try {
                await fetch(`${base}/${btn.dataset.cancel}/cancel`, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                });
            } catch (err) { /* abaikan */ }

            window.location.reload();
        }));
})();
</script>
@endpush