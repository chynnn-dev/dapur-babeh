<form method="POST" action="{{ $action }}" enctype="multipart/form-data"
      class="max-w-lg space-y-4 rounded-xl bg-white p-6 shadow">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div>
        <label for="category_id" class="block text-sm font-medium">Kategori</label>
        <select id="category_id" name="category_id" required
                class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
            <option value="">Pilih kategori</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $menu->category_id) == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        @error('category_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="name" class="block text-sm font-medium">Nama Menu</label>
        <input id="name" type="text" name="name" value="{{ old('name', $menu->name) }}" required
               class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="price" class="block text-sm font-medium">Harga (Rp)</label>
        <input id="price" type="number" name="price" min="0" step="500" value="{{ old('price', $menu->price) }}" required
               class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
        @error('price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="image" class="block text-sm font-medium">Gambar (opsional, maks 2 MB)</label>
        @if ($menu->image_url)
            <img src="{{ $menu->image_url }}" alt="{{ $menu->name }}" class="mt-2 h-24 w-24 rounded-lg object-cover">
        @endif
        <input id="image" type="file" name="image" accept="image/png,image/jpeg,image/webp"
               class="mt-2 block w-full text-sm">
        @error('image') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <input type="hidden" name="is_available" value="0">
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_available" value="1" class="rounded border-gray-300"
                   @checked(old('is_available', $menu->exists ? $menu->is_available : true))>
            Tersedia
        </label>
    </div>

    <div class="flex gap-2 pt-2">
        <button class="rounded-lg bg-orange-600 px-5 py-2 text-sm font-semibold text-white hover:bg-orange-700">Simpan</button>
        <a href="{{ route('admin.menus.index') }}" class="rounded-lg border px-5 py-2 text-sm hover:bg-gray-50">Batal</a>
    </div>
</form>