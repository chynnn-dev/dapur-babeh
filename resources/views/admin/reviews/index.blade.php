{{-- resources/views/admin/reviews/index.blade.php
     Ganti 'layouts.admin' dengan nama file layout admin Anda
     (mis. resources/views/layouts/admin.blade.php -> 'layouts.admin'). --}}
@extends('layouts.admin')

@section('title', 'Ulasan Pelanggan')

@section('content')
<style>
    .rv-wrap { max-width: 64rem; }
    .rv-top { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: .5rem; margin-bottom: 1rem; }
    .rv-muted { opacity: .65; font-size: .9rem; }
    .rv-link { color: #0f5f46; font-weight: 700; text-decoration: none; }
    .rv-ok { background: #e3f4ea; color: #0b4a37; padding: .6rem 1rem; border-radius: .5rem; margin-bottom: 1rem; }
    .rv-item { background: #fff; border-radius: .75rem; padding: 1rem 1.25rem; margin-bottom: .8rem; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
    .rv-head { display: flex; justify-content: space-between; gap: .5rem; flex-wrap: wrap; align-items: baseline; }
    .rv-stars { color: #f58220; letter-spacing: .1em; margin-left: .3rem; }
    .rv-badge { font-size: .75rem; font-weight: 700; padding: .15rem .7rem; border-radius: 99px; }
    .rv-on { background: #e3f4ea; color: #0b4a37; }
    .rv-off { background: #fff1de; color: #9a5a00; }
    .rv-item p { margin: .5rem 0; white-space: pre-line; }
    .rv-actions { display: flex; gap: .5rem; align-items: center; }
    .rv-btn { font: inherit; font-size: .875rem; font-weight: 700; padding: .4rem 1rem; border-radius: .5rem; border: 0; cursor: pointer; }
    .rv-b-ok { background: #0f5f46; color: #fff; }
    .rv-b-ok:hover { background: #0b4a37; }
    .rv-b-hide { background: #e5e7eb; color: #1f2937; }
    .rv-b-del { background: #fde2e2; color: #a12626; }
</style>

<div class="rv-wrap">
    <div class="rv-top">
        <span class="rv-muted">{{ $pending }} ulasan menunggu persetujuan</span>
        <a class="rv-link" href="{{ url('/') }}" target="_blank" rel="noopener">Lihat Website &rarr;</a>
    </div>

    @if (session('ok'))
        <div class="rv-ok">{{ session('ok') }}</div>
    @endif

    @forelse ($reviews as $r)
        <div class="rv-item">
            <div class="rv-head">
                <div>
                    <b>{{ $r->name }}</b>
                    <span class="rv-stars">@for ($i = 1; $i <= 5; $i++){{ $i <= $r->rating ? '★' : '☆' }}@endfor</span>
                </div>
                <span class="rv-badge {{ $r->is_approved ? 'rv-on' : 'rv-off' }}">{{ $r->is_approved ? 'Tampil' : 'Menunggu' }}</span>
            </div>
            <p>{{ $r->comment }}</p>
            <div class="rv-head">
                <span class="rv-muted">{{ $r->created_at->format('d/m/Y H:i') }}</span>
                <div class="rv-actions">
                    <form method="POST" action="{{ route('admin.reviews.toggle', $r) }}">
                        @csrf @method('PATCH')
                        @if ($r->is_approved)
                            <button class="rv-btn rv-b-hide">Sembunyikan</button>
                        @else
                            <button class="rv-btn rv-b-ok">Setujui &amp; Tampilkan</button>
                        @endif
                    </form>
                    <form method="POST" action="{{ route('admin.reviews.destroy', $r) }}" onsubmit="return confirm('Hapus ulasan ini?')">
                        @csrf @method('DELETE')
                        <button class="rv-btn rv-b-del">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <p class="rv-muted">Belum ada ulasan masuk.</p>
    @endforelse

    {{ $reviews->links() }}
</div>
@endsection