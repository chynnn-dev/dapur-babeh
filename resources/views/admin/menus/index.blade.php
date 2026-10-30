@extends('layouts.admin')

@section('title', 'Menu')

@section('content')
    <div class="toolbar">
        <form method="GET" class="flex flex-wrap gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama menu"
                   class="w-56 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
            <select name="category" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <option value="">Semua kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            <button class="rounded-lg bg-gray-800 px-4 py-2 text-sm text-white">Filter</button>
        </form>

        <a href="{{ route('admin.menus.create') }}"
           class="rounded-lg bg-orange-600 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-orange-700">+ Tambah Menu</a>
    </div>

    <div class="overflow-x-auto rounded-xl bg-white shadow">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-600">
                <tr>
                    <th class="px-4 py-3">Gambar</th>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Kategori</th>
                    <th class="px-4 py-3">Harga</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($menus as $menu)
                    <tr>
                        <td class="px-4 py-3">
                            @if ($menu->image_url)
                                <img src="{{ $menu->image_url }}" alt="{{ $menu->name }}" class="h-12 w-12 rounded-lg object-cover">
                            @else
                                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gray-100 text-xs text-gray-400">N/A</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-medium">{{ $menu->name }}</td>
                        <td class="px-4 py-3">{{ $menu->category->name }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($menu->price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('admin.menus.availability', $menu) }}">
                                @csrf
                                @method('PATCH')
                                <button class="rounded-full px-3 py-1 text-xs font-medium {{ $menu->is_available ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $menu->is_available ? 'Tersedia' : 'Habis' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.menus.edit', $menu) }}" class="rounded-lg border px-3 py-1 hover:bg-gray-50">Edit</a>
                                <form method="POST" action="{{ route('admin.menus.destroy', $menu) }}"
                                      onsubmit="return confirm('Hapus menu {{ $menu->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-lg border border-red-300 px-3 py-1 text-red-600 hover:bg-red-50">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">Belum ada menu.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $menus->links() }}</div>
@endsection