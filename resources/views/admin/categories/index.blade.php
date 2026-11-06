@extends('layouts.admin')

@section('title', 'Kategori')

@section('content')
    <style>
        .ct { --g: #0f5f46; --gd: #0b4a37; --o: #f58220; --line: #ece6d9; --muted: #6b7280; max-width: 44rem; }
        .ct * { box-sizing: border-box; }
        .ct-card { background: #fff; border-radius: .9rem; box-shadow: 0 1px 3px rgba(15,95,70,.08), 0 6px 18px rgba(15,95,70,.05); }
        .ct-add { padding: 1rem 1.1rem; }
        .ct-add h3, .ct-head h3 { margin: 0; font-size: .95rem; font-weight: 800; color: var(--g); }
        .ct-add-row { display: flex; gap: .6rem; margin-top: .65rem; }
        .ct-input { flex: 1; min-width: 0; border: 1px solid #d9cfbd; border-radius: .5rem; padding: .55rem .75rem; font-size: .875rem; background: #fbf9f4; }
        .ct-input:focus { outline: 2px solid var(--o); border-color: var(--o); background: #fff; }
        .ct-btn { border: 0; border-radius: .5rem; padding: .55rem 1.1rem; font-size: .85rem; font-weight: 700; cursor: pointer; text-decoration: none; display: inline-block; }
        .ct-btn-add { background: var(--g); color: #fff; }
        .ct-btn-add:hover { background: var(--gd); }
        .ct-error { margin: .5rem 0 0; font-size: .82rem; color: #b91c1c; }

        .ct-list { margin-top: 1.1rem; overflow: hidden; }
        .ct-head { display: flex; justify-content: space-between; align-items: center; padding: .95rem 1.1rem; border-bottom: 1px solid var(--line); }
        .ct-head small { color: var(--muted); font-size: .75rem; }
        .ct-wrap { overflow-x: auto; }
        .ct-table { width: 100%; border-collapse: collapse; font-size: .875rem; }
        .ct-table th { text-align: left; padding: .7rem 1.1rem; font-size: .7rem; text-transform: uppercase; letter-spacing: .06em; color: var(--muted); background: #faf8f3; white-space: nowrap; }
        .ct-table td { padding: .75rem 1.1rem; border-top: 1px solid var(--line); vertical-align: middle; }
        .ct-table tbody tr:hover { background: #fbf9f4; }
        .ct-name { font-weight: 700; color: #111827; }
        .ct-count { display: inline-block; padding: .15rem .7rem; border-radius: 99px; font-size: .75rem; font-weight: 700; background: #e9f3ee; color: var(--g); }
        .ct-actions { display: flex; justify-content: flex-end; gap: .45rem; }
        .ct-actions form { margin: 0; }
        .ct-act { border-radius: .45rem; padding: .35rem .85rem; font-size: .8rem; font-weight: 700; cursor: pointer; text-decoration: none; background: #fff; }
        .ct-edit { border: 1px solid #cfe3d9; color: var(--g); }
        .ct-edit:hover { background: #e9f3ee; }
        .ct-del { border: 1px solid #f3c1c1; color: #b91c1c; }
        .ct-del:hover { background: #fde2e2; }
        .ct-empty { text-align: center; color: var(--muted); padding: 2rem 1rem !important; }

        @media (max-width: 480px) {
            .ct-add-row { flex-direction: column; }
        }
    </style>

    <div class="ct">
        {{-- Tambah kategori --}}
        <form method="POST" action="{{ route('admin.categories.store') }}" class="ct-card ct-add">
            @csrf
            <h3>Tambah Kategori</h3>
            <div class="ct-add-row">
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama kategori baru" required class="ct-input">
                <button class="ct-btn ct-btn-add">Tambah</button>
            </div>
            @error('name') <p class="ct-error">{{ $message }}</p> @enderror
        </form>

        {{-- Daftar kategori --}}
        <div class="ct-card ct-list">
            <div class="ct-head">
                <h3>Daftar Kategori</h3>
                <small>{{ $categories->count() }} kategori</small>
            </div>
            <div class="ct-wrap">
                <table class="ct-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Jumlah Menu</th>
                            <th style="text-align:right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td class="ct-name">{{ $category->name }}</td>
                                <td><span class="ct-count">{{ $category->menus_count }} menu</span></td>
                                <td>
                                    <div class="ct-actions">
                                        <a href="{{ route('admin.categories.edit', $category) }}" class="ct-act ct-edit">Edit</a>
                                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                              onsubmit="return confirm('Hapus kategori {{ $category->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="ct-act ct-del">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="ct-empty">Belum ada kategori.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection