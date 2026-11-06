@extends('layouts.admin')

@section('title', 'Kelola User')

@section('content')
    <style>
        .us { --g: #0f5f46; --gd: #0b4a37; --o: #f58220; --line: #ece6d9; --muted: #6b7280; }
        .us * { box-sizing: border-box; }
        .us-card { background: #fff; border-radius: .9rem; box-shadow: 0 1px 3px rgba(15,95,70,.08), 0 6px 18px rgba(15,95,70,.05); }

        /* Toolbar */
        .us-bar { display: flex; flex-wrap: wrap; gap: .75rem; align-items: center; justify-content: space-between; padding: 1rem 1.1rem; margin-bottom: 1rem; }
        .us-search { display: flex; gap: .5rem; }
        .us-input { width: 16rem; max-width: 100%; border: 1px solid #d9cfbd; border-radius: .5rem; padding: .5rem .75rem; font-size: .85rem; background: #fbf9f4; }
        .us-input:focus { outline: 2px solid var(--o); border-color: var(--o); background: #fff; }
        .us-btn { border: 0; border-radius: .5rem; padding: .5rem 1.1rem; font-size: .85rem; font-weight: 700; cursor: pointer; text-decoration: none; display: inline-block; text-align: center; }
        .us-btn-search { background: var(--g); color: #fff; }
        .us-btn-search:hover { background: var(--gd); }
        .us-btn-add { background: var(--o); color: #fff; }
        .us-btn-add:hover { background: #d96d10; }

        /* Tabel */
        .us-head { display: flex; justify-content: space-between; align-items: center; padding: .95rem 1.1rem; border-bottom: 1px solid var(--line); }
        .us-head h3 { margin: 0; font-size: .95rem; font-weight: 800; color: var(--g); }
        .us-head small { color: var(--muted); font-size: .75rem; }
        .us-wrap { overflow-x: auto; }
        .us-table { width: 100%; border-collapse: collapse; font-size: .875rem; }
        .us-table th { text-align: left; padding: .7rem 1.1rem; font-size: .7rem; text-transform: uppercase; letter-spacing: .06em; color: var(--muted); background: #faf8f3; white-space: nowrap; }
        .us-table td { padding: .7rem 1.1rem; border-top: 1px solid var(--line); vertical-align: middle; }
        .us-table tbody tr:hover { background: #fbf9f4; }
        .us-user { display: flex; align-items: center; gap: .75rem; }
        .us-avatar { width: 2.4rem; height: 2.4rem; border-radius: 50%; display: grid; place-items: center; flex-shrink: 0; background: var(--g); color: #fff; font-weight: 800; font-size: .95rem; }
        .us-name { font-weight: 700; color: #111827; }
        .us-me { margin-left: .4rem; padding: .05rem .5rem; border-radius: 99px; font-size: .65rem; font-weight: 800; background: #e9f3ee; color: var(--g); vertical-align: middle; }
        .us-username { color: var(--muted); }
        .us-role { display: inline-block; padding: .2rem .8rem; border-radius: 99px; font-size: .75rem; font-weight: 700; background: #fff1de; color: #9a5a00; white-space: nowrap; }

        .us-actions { display: flex; justify-content: flex-end; gap: .45rem; }
        .us-actions form { margin: 0; }
        .us-act { border-radius: .45rem; padding: .35rem .85rem; font-size: .8rem; font-weight: 700; cursor: pointer; text-decoration: none; background: #fff; }
        .us-edit { border: 1px solid #cfe3d9; color: var(--g); }
        .us-edit:hover { background: #e9f3ee; }
        .us-del { border: 1px solid #f3c1c1; color: #b91c1c; }
        .us-del:hover { background: #fde2e2; }
        .us-empty { text-align: center; color: var(--muted); padding: 2.2rem 1rem !important; }
        .us-pager { margin-top: 1rem; }

        @media (max-width: 560px) {
            .us-search { width: 100%; }
            .us-input { flex: 1; width: 100%; }
            .us-btn-add { width: 100%; }
        }
    </style>

    <div class="us">
        {{-- Cari & tambah --}}
        <div class="us-card us-bar">
            <form method="GET" class="us-search">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / username" class="us-input">
                <button class="us-btn us-btn-search">Cari</button>
            </form>

            <a href="{{ route('admin.users.create') }}" class="us-btn us-btn-add">+ Tambah User</a>
        </div>

        {{-- Daftar user --}}
        <div class="us-card">
            <div class="us-head">
                <h3>Daftar User</h3>
                <small>{{ $users->total() }} user</small>
            </div>
            <div class="us-wrap">
                <table class="us-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th style="text-align:right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>
                                    <div class="us-user">
                                        <div class="us-avatar">{{ strtoupper(mb_substr($user->name, 0, 1)) }}</div>
                                        <div class="us-name">
                                            {{ $user->name }}
                                            @if ($user->is(auth()->user()))
                                                <span class="us-me">ANDA</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="us-username">{{ $user->username }}</td>
                                <td><span class="us-role">{{ \App\Models\User::ROLES[$user->role] }}</span></td>
                                <td>
                                    <div class="us-actions">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="us-act us-edit">Edit</a>

                                        @if (! $user->is(auth()->user()))
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                  onsubmit="return confirm('Hapus user {{ $user->username }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="us-act us-del">Hapus</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="us-empty">Belum ada data user.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="us-pager">{{ $users->links() }}</div>
    </div>
@endsection