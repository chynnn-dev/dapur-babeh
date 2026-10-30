@extends('layouts.kitchen')

@section('title', 'Stok Menu')

@section('content')
    <div class="page">
        <p class="hint">
            Tandai menu yang bahannya habis. Menu yang ditandai <strong>Habis</strong> otomatis hilang dari layar kasir.
        </p>

        @forelse ($categories as $category)
            <h2 class="cat-title">{{ $category->name }}</h2>

            <div class="stock-grid">
                @foreach ($category->menus as $menu)
                    <form method="POST" action="{{ route('kitchen.stock.toggle', $menu) }}"
                          class="stock-card {{ $menu->is_available ? '' : 'out' }}">
                        @csrf
                        @method('PATCH')

                        <div class="sname">{{ $menu->name }}</div>
                        <div class="sstate">{{ $menu->is_available ? 'Tersedia' : 'HABIS' }}</div>

                        <button type="submit" class="stock-btn {{ $menu->is_available ? 'mark-out' : 'mark-in' }}">
                            {{ $menu->is_available ? 'Tandai Habis' : 'Tandai Tersedia' }}
                        </button>
                    </form>
                @endforeach
            </div>
        @empty
            <p class="hint">Belum ada menu.</p>
        @endforelse
    </div>
@endsection