@extends('layouts.admin')

@section('title', 'Pengaturan')

@section('content')
    @php
        $badge = fn ($on) => 'padding:.25rem .75rem; border-radius:9999px; font-size:.75rem; font-weight:600;'
            . ($on ? 'background:#dcfce7; color:#15803d;' : 'background:#e5e7eb; color:#4b5563;');
        $btn = fn ($on) => 'padding:.45rem 1.1rem; border-radius:.5rem; font-size:.85rem; font-weight:600; color:#fff; border:0; cursor:pointer;'
            . ($on ? 'background:#dc2626;' : 'background:#16a34a;');
    @endphp

    {{-- Manajemen Meja --}}
    <div class="max-w-xl rounded-xl bg-white p-6 shadow" style="margin-bottom:1rem;">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="font-semibold">Manajemen Meja</h2>
                <p class="mt-1 text-sm text-gray-500">
                    ON: kasir wajib memilih nomor meja saat transaksi.
                    OFF: transaksi cepat tanpa meja (takeaway).
                </p>
            </div>
            <span style="{{ $badge($tableManagement) }}">{{ $tableManagement ? 'ON' : 'OFF' }}</span>
        </div>

        <div class="mt-4 text-sm text-gray-600">
            Total meja: <strong>{{ $tableCount }}</strong> &middot; Sedang terisi: <strong>{{ $occupiedCount }}</strong>
        </div>

        <form method="POST" action="{{ route('admin.settings.table-management') }}" style="margin-top:1.25rem;">
            @csrf
            @method('PATCH')
            <button type="submit" style="{{ $btn($tableManagement) }}">
                {{ $tableManagement ? 'Matikan Fitur Meja' : 'Aktifkan Fitur Meja' }}
            </button>
        </form>
    </div>

    {{-- Metode Pembayaran --}}
    <div class="max-w-xl rounded-xl bg-white p-6 shadow">
        <h2 class="font-semibold">Metode Pembayaran</h2>
        <p class="mt-1 text-sm text-gray-500">
            QRIS dan Debit dicatat manual: kasir memastikan dana sudah masuk sebelum mengonfirmasi di POS.
        </p>

        {{-- Tunai --}}
        <div style="display:flex; justify-content:space-between; align-items:center; padding:.9rem 0; border-top:1px solid #e5e7eb; margin-top:1rem;">
            <div class="font-medium">Tunai</div>
            <span style="{{ $badge(true) }}">Selalu aktif</span>
        </div>

        {{-- QRIS --}}
        <div style="padding:.9rem 0; border-top:1px solid #e5e7eb;">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem;">
                <div class="font-medium">QRIS</div>
                <div style="display:flex; align-items:center; gap:.6rem;">
                    <span style="{{ $badge($qrisOn) }}">{{ $qrisOn ? 'ON' : 'OFF' }}</span>
                    <form method="POST" action="{{ route('admin.settings.payment', 'qris') }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" style="{{ $btn($qrisOn) }}">{{ $qrisOn ? 'Matikan' : 'Aktifkan' }}</button>
                    </form>
                </div>
            </div>

            @if ($qrisImage)
                <img src="{{ $qrisImage }}" alt="QRIS toko"
                     style="margin-top:.9rem; max-width:12rem; max-height:12rem; object-fit:contain; border:1px solid #e5e7eb; border-radius:.6rem; padding:.3rem;">
            @else
                <p class="mt-3 text-sm text-gray-500">Belum ada gambar QRIS toko.</p>
            @endif

            <form method="POST" action="{{ route('admin.settings.qris.upload') }}" enctype="multipart/form-data"
                  style="margin-top:.8rem; display:flex; flex-wrap:wrap; gap:.5rem; align-items:center;">
                @csrf
                <input type="file" name="qris_image" accept="image/png,image/jpeg,image/webp" class="text-sm">
                <button type="submit" class="rounded-lg border px-3 py-1 text-sm hover:bg-gray-50">
                    {{ $qrisImage ? 'Ganti Gambar' : 'Unggah Gambar' }}
                </button>
            </form>
            @error('qris_image') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror

            @if ($qrisImage)
                <form method="POST" action="{{ route('admin.settings.qris.remove') }}" style="margin-top:.5rem;"
                      onsubmit="return confirm('Hapus gambar QRIS? QRIS juga akan dinonaktifkan.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-lg border border-red-300 px-3 py-1 text-sm text-red-600 hover:bg-red-50">Hapus Gambar</button>
                </form>
            @endif
        </div>

        {{-- Debit --}}
        <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; padding:.9rem 0 0; border-top:1px solid #e5e7eb;">
            <div>
                <div class="font-medium">Debit</div>
                <div class="text-xs text-gray-500">Diproses di mesin EDC terpisah.</div>
            </div>
            <div style="display:flex; align-items:center; gap:.6rem;">
                <span style="{{ $badge($debitOn) }}">{{ $debitOn ? 'ON' : 'OFF' }}</span>
                <form method="POST" action="{{ route('admin.settings.payment', 'debit') }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" style="{{ $btn($debitOn) }}">{{ $debitOn ? 'Matikan' : 'Aktifkan' }}</button>
                </form>
            </div>
        </div>
    </div>
@endsection