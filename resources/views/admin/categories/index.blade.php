@extends('layouts.admin')

@section('title', 'Kategori')

@section('content')
    <form method="POST" action="{{ route('admin.categories.store') }}" class="mb-2 flex max-w-lg gap-2">
        @csrf
        <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama kategori baru" required
               class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
        <button class="rounded-lg bg-orange-600 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-700">Tambah</button>
    </form>
    @error('name') <p class="mb-4 text-sm text-red-600">{{ $message }}</p> @enderror

    <div class="mt-4 max-w-2xl overflow-x-auto rounded-xl bg-white shadow">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-600">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Jumlah Menu</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($categories as $category)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $category->name }}</td>
                        <td class="px-4 py-3">{{ $category->menus_count }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="rounded-lg border px-3 py-1 hover:bg-gray-50">Edit</a>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                      onsubmit="return confirm('Hapus kategori {{ $category->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-lg border border-red-300 px-3 py-1 text-red-600 hover:bg-red-50">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-4 py-8 text-center text-gray-500">Belum ada kategori.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection