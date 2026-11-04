@extends('layouts.admin')

@section('title', 'Halaman Depan')

@section('content')
    @php
        $fields = [
            'site_name'        => ['Nama Restoran', 'text', 'Tampil di logo dan judul halaman.'],
            'hero_title'       => ['Judul Utama (Hero)', 'text', ''],
            'hero_subtitle'    => ['Sub Judul (Hero)', 'text', ''],
            'about_title'      => ['Judul Bagian Tentang', 'text', ''],
            'about_text'       => ['Teks Tentang Kami', 'textarea', 'Tekan Enter untuk membuat paragraf baru.'],
            'open_hours'       => ['Jam Buka', 'text', 'Contoh: Buka setiap hari 10.00 – 22.00 WIB'],
            'address'          => ['Alamat', 'textarea', ''],
            'maps_url'         => ['Link Google Maps', 'url', 'Opsional. Tempel link dari tombol Bagikan di Google Maps.'],
            'whatsapp_number'  => ['Nomor WhatsApp Reservasi', 'text', 'Contoh: 081234567890 atau +6281234567890. Kosongkan untuk menyembunyikan tombol reservasi.'],
            'whatsapp_message' => ['Pesan Awal WhatsApp', 'textarea', 'Terisi otomatis saat pelanggan menekan tombol Reservasi.'],
        ];
    @endphp

    <div style="max-width:42rem; margin-bottom:.75rem;">
        <a href="{{ route('home') }}" target="_blank" class="text-sm" style="color:#ea580c;">Lihat halaman depan &rarr;</a>
    </div>

    <form method="POST" action="{{ route('admin.front.update') }}" class="max-w-2xl space-y-4 rounded-xl bg-white p-6 shadow">
        @csrf
        @method('PUT')

        @foreach ($fields as $key => [$label, $type, $hint])
            <div>
                <label for="{{ $key }}" class="block text-sm font-medium">{{ $label }}</label>

                @if ($type === 'textarea')
                    <textarea id="{{ $key }}" name="{{ $key }}" rows="{{ $key === 'about_text' ? 6 : 3 }}"
                              class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">{{ old($key, $c[$key] ?? '') }}</textarea>
                @else
                    <input id="{{ $key }}" type="{{ $type }}" name="{{ $key }}" value="{{ old($key, $c[$key] ?? '') }}"
                           class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                @endif

                @if ($hint)
                    <p class="mt-1 text-xs text-gray-500">{{ $hint }}</p>
                @endif
                @error($key) <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        @endforeach

        <div class="pt-2">
            <button class="rounded-lg bg-orange-600 px-5 py-2 text-sm font-semibold text-white hover:bg-orange-700">Simpan Perubahan</button>
        </div>
    </form>
@endsection