@extends('layouts.admin')

@section('title', 'Menu')

@section('content')
    <style>
        .mn { --g: #0f5f46; --gd: #0b4a37; --o: #f58220; --line: #ece6d9; --muted: #6b7280; }
        .mn * { box-sizing: border-box; }
        .mn-card { background: #fff; border-radius: .9rem; box-shadow: 0 1px 3px rgba(15,95,70,.08), 0 6px 18px rgba(15,95,70,.05); }

        /* Toolbar */
        .mn-bar { display: flex; flex-wrap: wrap; gap: .75rem; align-items: center; justify-content: space-between; padding: 1rem 1.1rem; margin-bottom: 1rem; }
        .mn-filter { display: flex; flex-wrap: wrap; gap: .5rem; }
        .mn-input { border: 1px solid #d9cfbd; border-radius: .5rem; padding: .5rem .75rem; font-size: .85rem; background: #fbf9f4; }
        .mn-input[type="text"] { width: 14rem; max-width: 100%; }
        .mn-input:focus { outline: 2px solid var(--o); border-color: var(--o); background: #fff; }
        .mn-btn { border: 0; border-radius: .5rem; padding: .5rem 1.1rem; font-size: .85rem; font-weight: 700; cursor: pointer; text-decoration: none; display: inline-block; text-align: center; }
        .mn-btn-filter { background: var(--g); color: #fff; }
        .mn-btn-filter:hover { background: var(--gd); }
        .mn-btn-add { background: var(--o); color: #fff; }
        .mn-btn-add:hover { background: #d96d10; }

        /* Tabel */
        .mn-head { display: flex; justify-content: space-between; align-items: center; padding: .95rem 1.1rem; border-bottom: 1px solid var(--line); }
        .mn-head h3 { margin: 0; font-size: .95rem; font-weight: 800; color: var(--g); }
        .mn-head small { color: var(--muted); font-size: .75rem; }
        .mn-wrap { overflow-x: auto; }
        .mn-table { width: 100%; border-collapse: collapse; font-size: .875rem; }
        .mn-table th { text-align: left; padding: .7rem 1.1rem; font-size: .7rem; text-transform: uppercase; letter-spacing: .06em; color: var(--muted); background: #faf8f3; white-space: nowrap; }
        .mn-table td { padding: .7rem 1.1rem; border-top: 1px solid var(--line); vertical-align: middle; }
        .mn-table tbody tr:hover { background: #fbf9f4; }
        .mn-img { width: 3.25rem; height: 3.25rem; border-radius: .6rem; object-fit: cover; display: block; box-shadow: 0 1px 3px rgba(0,0,0,.15); }
        .mn-noimg { width: 3.25rem; height: 3.25rem; border-radius: .6rem; display: grid; place-items: center; background: #f0ebe0; color: #a89f8c; font-size: .7rem; font-weight: 700; }
        .mn-name { font-weight: 700; color: #111827; }
        .mn-cat { display: inline-block; padding: .15rem .7rem; border-radius: 99px; font-size: .75rem; font-weight: 700; background: #e9f3ee; color: var(--g); white-space: nowrap; }
        .mn-price { font-weight: 700; white-space: nowrap; }

        .mn-stock { margin: 0; }
        .mn-pill { border: 0; border-radius: 99px; padding: .25rem .8rem; font-size: .75rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: .35rem; white-space: nowrap; }
        .mn-pill::before { content: ""; width: .45rem; height: .45rem; border-radius: 50%; background: currentColor; }
        .mn-ok { background: #e3f4ea; color: #0b4a37; }
        .mn-ok:hover { background: #cfeadb; }
        .mn-out { background: #fde2e2; color: #a12626; }
        .mn-out:hover { background: #fbcfcf; }

        .mn-actions { display: flex; justify-content: flex-end; gap: .45rem; }
        .mn-actions form { margin: 0; }
        .mn-act { border-radius: .45rem; padding: .35rem .85rem; font-size: .8rem; font-weight: 700; cursor: pointer; text-decoration: none; background: #fff; }
        .mn-edit { border: 1px solid #cfe3d9; color: var(--g); }
        .mn-edit:hover { background: #e9f3ee; }
        .mn-del { border: 1px solid #f3c1c1; color: #b91c1c; }
        .mn-del:hover { background: #fde2e2; }
        .mn-empty { text-align: center; color: var(--muted); padding: 2.2rem 1rem !important; }
        .mn-pager { margin-top: 1rem; }

        @media (max-width: 560px) {
            .mn-filter { width: 100%; }
            .mn-input[type="text"], .mn-input { flex: 1 1 100%; width: 100%; }
            .mn-btn-add { width: 100%; }
        }
    </style>

    <div class="mn">
        {{-- Filter & tambah --}}
        <div class="mn-card mn-bar">
            <form method="GET" class="mn-filter">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama menu" class="mn-input">
                <select name="category" class="mn-input">
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <button class="mn-btn mn-btn-filter">Filter</button>
            </form>

            <a href="{{ route('admin.menus.create') }}" class="mn-btn mn-btn-add">+ Tambah Menu</a>
        </div>

        {{-- Daftar menu --}}
        <div class="mn-card">
            <div class="mn-head">
                <h3>Daftar Menu</h3>
                <small>{{ $menus->total() }} menu</small>
            </div>
            <div class="mn-wrap">
                <table class="mn-table">
                    <thead>
                        <tr>
                            <th>Gambar</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Status</th>
                            <th style="text-align:right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($menus as $menu)
                            <tr>
                                <td>
                                    @if ($menu->image_url)
                                        <img src="{{ $menu->image_url }}" alt="{{ $menu->name }}" class="mn-img">
                                    @else
                                        <div class="mn-noimg">N/A</div>
                                    @endif
                                </td>
                                <td class="mn-name">{{ $menu->name }}</td>
                                <td><span class="mn-cat">{{ $menu->category->name }}</span></td>
                                <td class="mn-price">Rp {{ number_format($menu->price, 0, ',', '.') }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.menus.availability', $menu) }}" class="mn-stock">
                                        @csrf
                                        @method('PATCH')
                                        <button class="mn-pill {{ $menu->is_available ? 'mn-ok' : 'mn-out' }}" title="Klik untuk mengubah status">
                                            {{ $menu->is_available ? 'Tersedia' : 'Habis' }}
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <div class="mn-actions">
                                        <a href="{{ route('admin.menus.edit', $menu) }}" class="mn-act mn-edit">Edit</a>
                                        <form method="POST" action="{{ route('admin.menus.destroy', $menu) }}"
                                              onsubmit="return confirm('Hapus menu {{ $menu->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="mn-act mn-del">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="mn-empty">Belum ada menu.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mn-pager">{{ $menus->links() }}</div>
    </div>
@endsection