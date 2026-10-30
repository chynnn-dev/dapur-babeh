@extends('layouts.admin')

@section('title', 'Edit Meja')

@section('content')
    <form method="POST" action="{{ route('admin.tables.update', $table) }}" class="max-w-lg space-y-4 rounded-xl bg-white p-6 shadow">
        @csrf
        @method('PUT')

        <div>
            <label for="table_number" class="block text-sm font-medium">Nomor Meja</label>
            <input id="table_number" type="text" name="table_number" value="{{ old('table_number', $table->table_number) }}" required
                   class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
            @error('table_number') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="status" class="block text-sm font-medium">Status</label>
            <select id="status" name="status"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                <option value="available" @selected(old('status', $table->status) === 'available')>Kosong</option>
                <option value="occupied" @selected(old('status', $table->status) === 'occupied')>Terisi</option>
            </select>
            @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-2">
            <button class="rounded-lg bg-orange-600 px-5 py-2 text-sm font-semibold text-white hover:bg-orange-700">Simpan</button>
            <a href="{{ route('admin.tables.index') }}" class="rounded-lg border px-5 py-2 text-sm hover:bg-gray-50">Batal</a>
        </div>
    </form>
@endsection