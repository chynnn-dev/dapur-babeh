@extends('layouts.admin')

@section('title', 'Meja')

@section('content')
    @php
        $badge = [
            'available' => ['Kosong',    '#dcfce7', '#166534'],
            'occupied'  => ['Terisi',    '#fee2e2', '#991b1b'],
            'reserved'  => ['Reservasi', '#fef3c7', '#92400e'],
        ];
    @endphp

    @unless ($featureOn)
        <div style="margin-bottom:1rem; max-width:42rem; padding:.75rem 1rem; border-radius:.5rem; background:#fef3c7; color:#92400e; font-size:.875rem;">
            Fitur Manajemen Meja sedang <strong>OFF</strong>: semua meja tidak dipakai di kasir.
            Daftar dan status meja tetap tersimpan. Aktifkan di menu Pengaturan.
        </div>
    @endunless

    <div style="display:flex; flex-wrap:wrap; gap:.5rem; margin-bottom:1rem; font-size:.8rem; font-weight:600;">
        <span style="padding:.25rem .75rem; border-radius:9999px; background:#dcfce7; color:#166534;">Kosong: {{ $tables->where('status', 'available')->count() }}</span>
        <span style="padding:.25rem .75rem; border-radius:9999px; background:#fee2e2; color:#991b1b;">Terisi: {{ $tables->where('status', 'occupied')->count() }}</span>
        <span style="padding:.25rem .75rem; border-radius:9999px; background:#fef3c7; color:#92400e;">Reservasi: {{ $tables->where('status', 'reserved')->count() }}</span>
    </div>

    <form method="POST" action="{{ route('admin.tables.store') }}" class="mb-2 flex max-w-lg gap-2">
        @csrf
        <input type="text" name="table_number" value="{{ old('table_number') }}" placeholder="Nomor meja baru (mis. 6 atau A1)" required
               class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
        <button class="rounded-lg bg-orange-600 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-700">Tambah</button>
    </form>
    @error('table_number') <p class="mb-4 text-sm text-red-600">{{ $message }}</p> @enderror

    <p class="mt-2 text-xs text-gray-500">Status meja diatur oleh kasir (reservasi) dan sistem (terisi/kosong saat transaksi dan pesanan selesai).</p>

    <div class="mt-4 max-w-2xl overflow-x-auto rounded-xl bg-white shadow" @unless ($featureOn) style="opacity:.6" @endunless>
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
                    @php [$label, $bg, $fg] = $badge[$table->status]; @endphp
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $table->table_number }}</td>
                        <td class="px-4 py-3">
                            <span style="padding:.25rem .75rem; border-radius:9999px; font-size:.75rem; font-weight:600; background:{{ $bg }}; color:{{ $fg }};">
                                {{ $label }}
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