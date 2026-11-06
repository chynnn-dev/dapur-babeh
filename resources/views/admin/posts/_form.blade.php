<form method="POST" action="{{ $action }}" enctype="multipart/form-data"
      class="max-w-lg space-y-4 rounded-xl bg-white p-6 shadow">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div>
        <label for="type" class="block text-sm font-medium">Jenis Postingan</label>
        <select id="type" name="type" required
                class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
            @foreach (\App\Models\Post::TYPES as $key => $label)
                <option value="{{ $key }}" @selected(old('type', $post->type) === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <p class="mt-1 text-xs text-gray-500">
            Slide Hero: latar paling atas, bisa berupa gambar atau video (gambar landscape, lebar minimal 1600 px).
            Menu: gambar menu (disarankan potret 3:4). Galeri: foto suasana (disarankan 4:3).
        </p>
        @error('type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="title" class="block text-sm font-medium">Judul (opsional)</label>
        <input id="title" type="text" name="title" value="{{ old('title', $post->title) }}"
               class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
        <p class="mt-1 text-xs text-gray-500">Tampil sebagai keterangan di Menu dan Galeri. Tidak dipakai untuk Slide Hero.</p>
        @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="caption" class="block text-sm font-medium">Keterangan (opsional)</label>
        <textarea id="caption" name="caption" rows="2"
                  class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">{{ old('caption', $post->caption) }}</textarea>
        @error('caption') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="image" class="block text-sm font-medium">Gambar (maks 4 MB)</label>
        @if ($post->exists)
            <img src="{{ $post->image_url }}" alt="" class="mt-2 rounded-lg object-cover" style="width:10rem; height:7rem;">
            <p class="mt-1 text-xs text-gray-500">Pilih file baru hanya jika ingin mengganti gambar.</p>
        @endif
        <input id="image" type="file" name="image" accept="image/png,image/jpeg,image/webp"
               @if (! $post->exists) required @endif class="mt-2 block w-full text-sm">
        <p class="mt-1 text-xs text-gray-500">Untuk Slide Hero berisi video, gambar ini menjadi sampul saat video dimuat.</p>
        @error('image') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Video (hanya untuk Slide Hero) --}}
    <div id="video-box">
        <label for="video" class="block text-sm font-medium">Video Hero (opsional, MP4 atau WebM, maks 30 MB)</label>

        @if ($post->video_url)
            <video src="{{ $post->video_url }}" controls muted playsinline
                   style="margin-top:.5rem; max-width:100%; max-height:10rem; border-radius:.5rem;"></video>
            <label class="mt-2 flex items-center gap-2 text-sm">
                <input type="checkbox" name="remove_video" value="1" class="rounded border-gray-300">
                Hapus video ini
            </label>
        @endif

        <input id="video" type="file" name="video" accept="video/mp4,video/webm" class="mt-2 block w-full text-sm">
        <p class="mt-1 text-xs text-gray-500">
            Diputar otomatis tanpa suara sebagai latar. Disarankan 10 sampai 20 detik, resolusi maksimal 1080p.
        </p>
        @error('video') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="sort_order" class="block text-sm font-medium">Urutan Tampil</label>
        <input id="sort_order" type="number" name="sort_order" min="0" value="{{ old('sort_order', $post->sort_order ?? 0) }}"
               class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
        <p class="mt-1 text-xs text-gray-500">Angka kecil tampil lebih dulu. Jika sama, yang terbaru di depan.</p>
        @error('sort_order') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <input type="hidden" name="is_published" value="0">
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_published" value="1" class="rounded border-gray-300"
                   @checked(old('is_published', $post->exists ? $post->is_published : true))>
            Tampilkan di halaman depan
        </label>
    </div>

    <div class="flex gap-2 pt-2">
        <button class="rounded-lg bg-orange-600 px-5 py-2 text-sm font-semibold text-white hover:bg-orange-700">Simpan</button>
        <a href="{{ route('admin.posts.index') }}" class="rounded-lg border px-5 py-2 text-sm hover:bg-gray-50">Batal</a>
    </div>
</form>

<script>
    (function () {
        const type = document.getElementById('type');
        const box  = document.getElementById('video-box');
        const sync = () => { box.hidden = type.value !== 'hero'; };
        type.addEventListener('change', sync);
        sync();
    })();
</script>