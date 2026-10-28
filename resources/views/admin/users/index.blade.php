@extends('layouts.admin')

@section('title', 'Kelola User')

@section('content')
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="GET" class="flex gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / username"
                   class="w-64 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
            <button class="rounded-lg bg-gray-800 px-4 py-2 text-sm text-white">Cari</button>
        </form>

        <a href="{{ route('admin.users.create') }}"
           class="rounded-lg bg-orange-600 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-orange-700">
            + Tambah User
        </a>
    </div>

    <div class="overflow-x-auto rounded-xl bg-white shadow">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-600">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Username</th>
                    <th class="px-4 py-3">Role</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($users as $user)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                        <td class="px-4 py-3">{{ $user->username }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full bg-orange-100 px-3 py-1 text-xs font-medium text-orange-700">
                                {{ \App\Models\User::ROLES[$user->role] }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="rounded-lg border px-3 py-1 hover:bg-gray-50">Edit</a>

                                @if (! $user->is(auth()->user()))
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                          onsubmit="return confirm('Hapus user {{ $user->username }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-lg border border-red-300 px-3 py-1 text-red-600 hover:bg-red-50">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">Belum ada data user.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
@endsection