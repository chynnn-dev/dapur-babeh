@extends('layouts.admin')

@section('title', 'Meja')

@section('content')
    @php
        $badge = [
            'available' => ['Kosong',    'tb-free'],
            'occupied'  => ['Terisi',    'tb-busy'],
            'reserved'  => ['Reservasi', 'tb-resv'],
        ];
    @endphp

    <style>
        .tb { --g: #0f5f46; --gd: #0b4a37; --o: #f58220; --line: #ece6d9; --muted: #6b7280; max-width: 44rem; }
        .tb * { box-sizing: border-box; }
        .tb-card { background: #fff; border-radius: .9rem; box-shadow: 0 1px 3px rgba(15,95,70,.08), 0 6px 18px rgba(15,95,70,.05); }

        .tb-warn { display: flex; gap: .6rem; align-items: flex-start; margin-bottom: 1rem; padding: .8rem 1rem; border-radius: .6rem; background: #fff4e5; border-left: 4px solid var(--o); color: #7a4300; font-size: .85rem; line-height: 1.5; }
        .tb-warn svg { flex-shrink: 0; margin-top: .15rem; }

        /* Ringkasan status */
        .tb-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: .75rem; margin-bottom: 1rem; }
        .tb-stat { padding: .85rem 1rem; text-align: center; position: relative; overflow: hidden; }
        .tb-stat::before { content: ""; position: absolute; left: 0; right: 0; top: 0; height: 4px; background: var(--c); }
        .tb-stat b { display: block; font-size: 1.6rem; font-weight: 800; line-height: 1.2; color: #111827; }
        .tb-stat span { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: var(--muted); }

        /* Tambah */
        .tb-add { padding: 1rem 1.1rem; }
        .tb-add h3, .tb-head h3 { margin: 0; font-size: .95rem; font-weight: 800; color: var(--g); }
        .tb-add-row { display: flex; gap: .6rem; margin-top: .65rem; }
        .tb-input { flex: 1; min-width: 0; border: 1px solid #d9cfbd; border-radius: .5rem; padding: .55rem .75rem; font-size: .875rem; background: #fbf9f4; }
        .tb-input:focus { outline: 2px solid var(--o); border-color: var(--o); background: #fff; }
        .tb-btn { border: 0; border-radius: .5rem; padding: .55rem 1.1rem; font-size: .85rem; font-weight: 700; cursor: pointer; background: var(--g); color: #fff; }
        .tb-btn:hover { background: var(--gd); }
        .tb-error { margin: .5rem 0 0; font-size: .82rem; color: #b91c1c; }
        .tb-hint { margin: .65rem 0 0; font-size: .75rem; color: var(--muted); line-height: 1.5; }

        /* Daftar */
        .tb-list { margin-top: 1.1rem; overflow: hidden; }
        .tb-head { display: flex; justify-content: space-between; align-items: center; padding: .95rem 1.1rem; border-bottom: 1px solid var(--line); }
        .tb-head small { color: var(--muted); font-size: .75rem; }
        .tb-wrap { overflow-x: auto; }
        .tb-table { width: 100%; border-collapse: collapse; font-size: .875rem; }
        .tb-table th { text-align: left; padding: .7rem 1.1rem; font-size: .7rem; text-transform: uppercase; letter-spacing: .06em; color: var(--muted); background: #faf8f3; white-space: nowrap; }
        .tb-table td { padding: .75rem 1.1rem; border-top: 1px solid var(--line); vertical-align: middle; }
        .tb-table tbody tr:hover { background: #fbf9f4; }
        .tb-num { display: inline-grid; place-items: center; min-width: 2.2rem; height: 2.2rem; padding: 0 .5rem; border-radius: .6rem; background: #e9f3ee; color: var(--g); font-weight: 800; }
        .tb-badge { display: inline-flex; align-items: center; gap: .35rem; padding: .2rem .8rem; border-radius: 99px; font-size: .75rem; font-weight: 700; white-space: nowrap; }
        .tb-badge::before { content: ""; width: .45rem; height: .45rem; border-radius: 50%; background: currentColor; }
        .tb-free { background: #e3f4ea; color: #166534; }
        .tb-busy { background: #fde2e2; color: #991b1b; }
        .tb-resv { background: #fff1de; color: #92400e; }

        .tb-actions { display: flex; justify-content: flex-end; gap: .45rem; }
        .tb-actions form { margin: 0; }
        .tb-act { border-radius: .45rem; padding: .35rem .85rem; font-size: .8rem; font-weight: 700; cursor: pointer; text-decoration: none; background: #fff; }
        .tb-edit { border: 1px solid #cfe3d9; color: var(--g); }
        .tb-edit:hover { background: #e9f3ee; }
        .tb-del { border: 1px solid #f3c1c1; color: #b91c1c; }
        .tb-del:hover { background: #fde2e2; }
        .tb-empty { text-align: center; color: var(--muted); padding: 2rem 1rem !important; }

        @media (max-width: 480px) {
            .tb-add-row { flex-direction: column; }
        }
    </style>

    <div class="tb">

        @unless ($featureOn)
            <div class="tb-warn" role="note">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2 1 21h22L12 2zm1 15h-2v-2h2v2zm0-4h-2V9h2v4z"/></svg>
                <div>
                    Fitur Manajemen Meja sedang <strong>OFF</strong>: semua meja tidak dipakai di kasir.
                    Daftar dan status meja tetap tersimpan. Aktifkan di menu Pengaturan.
                </div>
            </div>
        @endunless

        {{-- Ringkasan status --}}
        <div class="tb-stats">
            <div class="tb-card tb-stat" style="--c:#22a06b">
                <b>{{ $tables->where('status', 'available')->count() }}</b>
                <span>Kosong</span>
            </div>
            <div class="tb-card tb-stat" style="--c:#dc2626">
                <b>{{ $tables->where('status', 'occupied')->count() }}</b>
                <span>Terisi</span>
            </div>
            <div class="tb-card tb-stat" style="--c:#f58220">
                <b>{{ $tables->where('status', 'reserved')->count() }}</b>
                <span>Reservasi</span>
            </div>
        </div>

        {{-- Tambah meja --}}
        <form method="POST" action="{{ route('admin.tables.store') }}" class="tb-card tb-add">
            @csrf
            <h3>Tambah Meja</h3>
            <div class="tb-add-row">
                <input type="text" name="table_number" value="{{ old('table_number') }}"
                       placeholder="Nomor meja baru (mis. 6 atau A1)" required class="tb-input">
                <button class="tb-btn">Tambah</button>
            </div>
            @error('table_number') <p class="tb-error">{{ $message }}</p> @enderror
            <p class="tb-hint">Status meja diatur oleh kasir (reservasi) dan sistem (terisi/kosong saat transaksi dan pesanan selesai).</p>
        </form>

        {{-- Daftar meja --}}
        <div class="tb-card tb-list" @unless ($featureOn) style="opacity:.6" @endunless>
            <div class="tb-head">
                <h3>Daftar Meja</h3>
                <small>{{ $tables->count() }} meja</small>
            </div>
            <div class="tb-wrap">
                <table class="tb-table">
                    <thead>
                        <tr>
                            <th>Nomor Meja</th>
                            <th>Status</th>
                            <th style="text-align:right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tables as $table)
                            @php [$label, $cls] = $badge[$table->status]; @endphp
                            <tr>
                                <td><span class="tb-num">{{ $table->table_number }}</span></td>
                                <td><span class="tb-badge {{ $cls }}">{{ $label }}</span></td>
                                <td>
                                    <div class="tb-actions">
                                        <a href="{{ route('admin.tables.edit', $table) }}" class="tb-act tb-edit">Edit</a>
                                        <form method="POST" action="{{ route('admin.tables.destroy', $table) }}"
                                              onsubmit="return confirm('Hapus meja {{ $table->table_number }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="tb-act tb-del">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="tb-empty">Belum ada meja.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection