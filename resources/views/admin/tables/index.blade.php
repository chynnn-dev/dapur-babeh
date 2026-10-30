@extends('layouts.admin')

@section('title', 'Meja')

@section('content')

    
    <div style="margin-bottom:1rem; max-width:42rem; padding:.75rem 1rem; border-radius:.5rem; background:#fef3c7; color:#92400e; font-size:.875rem;">
        Fitur Manajemen Meja sedang <strong>OFF</strong>. Daftar meja tetap bisa diatur,
        tetapi kasir belum memakainya. Aktifkan di menu Pengaturan.
    </div>

    <form method="POST" action="{{ route('admin.tables.store') }}" class="mb-2 flex max-w-lg gap-2">
        @csrf
        <input type="text" name="table_number" value="{{ old('table_number') }}" placeholder="Nomor meja baru (mis. 6 atau A1)" required
               class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
        <button class="rounded-lg bg-orange-600 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-700">Tambah</button>
    </form>
    @error('table_number') <p class="mb-4 text-sm text-red-600">{{ $message }}</p> @enderror

    <div class="mt-4 max-w-2xl overflow-x-auto rounded-xl bg-white shadow">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-600">
                <tr>
                    <th class="px-4 py-3">Nomor Meja</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($tables as $table)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $table->table_number }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-3 py-1 text-xs font-medium {{ $table->status === 'available' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $table->status === 'available' ? 'Kosong' : 'Terisi' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.tables.edit', $table) }}" class="rounded-lg border px-3 py-1 hover:bg-gray-50">Edit</a>
                                <form method="POST" action="{{ route('admin.tables.destroy', $table) }}"
                                      onsubmit="return confirm('Hapus meja {{ $table->table_number }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-lg border border-red-300 px-3 py-1 text-red-600 hover:bg-red-50">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-4 py-8 text-center text-gray-500">Belum ada meja.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection