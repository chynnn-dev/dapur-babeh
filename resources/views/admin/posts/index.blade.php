@extends('layouts.admin')

@section('title', 'Postingan')

@section('content')
    @php
        $tabs = ['' => 'Semua'] + \App\Models\Post::TYPES;
        $tabStyle = fn ($active) => 'padding:.35rem .9rem; border-radius:9999px; font-size:.85rem; font-weight:600; text-decoration:none;'
            . ($active ? 'background:#ea580c; color:#fff;' : 'background:#fff; color:#6b7280; border:1px solid #e5e7eb;');
    @endphp

    <div class="toolbar">
        <div style="display:flex; flex-wrap:wrap; gap:.4rem;">
            @foreach ($tabs as $key => $label)
                <a href="{{ route('admin.posts.index', $key ? ['type' => $key] : []) }}"
                   style="{{ $tabStyle(($type ?? '') === (string) $key) }}">{{ $label }}</a>
            @endforeach
        </div>

        <a href="{{ route('admin.posts.create', $type ? ['type' => $type] : []) }}"
           class="rounded-lg bg-orange-600 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-orange-700">+ Tambah Postingan</a>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(14rem, 1fr)); gap:1rem;">
        @forelse ($posts as $post)
            <div class="rounded-xl bg-white shadow" style="overflow:hidden;">
                <img src="{{ $post->image_url }}" alt="{{ $post->title }}"
                     style="width:100%; height:10rem; object-fit:cover; display:block;">

                <div style="padding:.75rem;">
                    <div style="display:flex; flex-wrap:wrap; gap:.4rem; margin-bottom:.4rem;">
                        <span style="padding:.1rem .6rem; border-radius:9999px; font-size:.7rem; font-weight:600; background:#ffedd5; color:#c2410c;">
                            {{ \App\Models\Post::TYPES[$post->type] ?? $post->type }}
                        </span>
                        @if ($post->video)
    <span style="padding:.1rem .6rem; border-radius:9999px; font-size:.7rem; font-weight:600; background:#e0f2fe; color:#0369a1;">Video</span>
@endif  
                        <span style="padding:.1rem .6rem; border-radius:9999px; font-size:.7rem; font-weight:600;
                                     background:{{ $post->is_published ? '#dcfce7' : '#e5e7eb' }}; color:{{ $post->is_published ? '#166534' : '#4b5563' }};">
                            {{ $post->is_published ? 'Tampil' : 'Disembunyikan' }}
                        </span>
                    </div>

                    <div class="font-medium">{{ $post->title ?: '(tanpa judul)' }}</div>
                    <div class="text-xs text-gray-500">Urutan: {{ $post->sort_order }}</div>

                    <div style="display:flex; gap:.5rem; margin-top:.75rem;">
                        <a href="{{ route('admin.posts.edit', $post) }}" class="rounded-lg border px-3 py-1 text-sm hover:bg-gray-50">Edit</a>
                        <form method="POST" action="{{ route('admin.posts.destroy', $post) }}"
                              onsubmit="return confirm('Hapus postingan ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="rounded-lg border border-red-300 px-3 py-1 text-sm text-red-600 hover:bg-red-50">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-xl bg-white p-8 text-center text-gray-500 shadow" style="grid-column:1 / -1;">Belum ada postingan.</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $posts->links() }}</div>
@endsection