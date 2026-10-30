@extends('layouts.admin')

@section('title', 'Edit Kategori')

@section('content')
    <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="max-w-lg space-y-4 rounded-xl bg-white p-6 shadow">
        @csrf
        @method('PUT')

        <div>
            <label for="name" class="block text-sm font-medium">Nama Kategori</label>
            <input id="name" type="text" name="name" value="{{ old('name', $category->name) }}" required
                   class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-2">
            <button class="rounded-lg bg-orange-600 px-5 py-2 text-sm font-semibold text-white hover:bg-orange-700">Simpan</button>
            <a href="{{ route('admin.categories.index') }}" class="rounded-lg border px-5 py-2 text-sm hover:bg-gray-50">Batal</a>
        </div>
    </form>
@endsection