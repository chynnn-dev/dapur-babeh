@extends('layouts.admin')

@section('title', 'Pengaturan')

@section('content')
    <div class="max-w-xl rounded-xl bg-white p-6 shadow">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="font-semibold">Manajemen Meja</h2>
                <p class="mt-1 text-sm text-gray-500">
                    ON: kasir wajib memilih nomor meja saat transaksi.
                    OFF: transaksi cepat tanpa meja (takeaway).
                </p>
            </div>

            <span style="padding:.25rem .75rem; border-radius:9999px; font-size:.75rem; font-weight:600;
                         background: {{ $tableManagement ? '#dcfce7' : '#e5e7eb' }};
                         color: {{ $tableManagement ? '#15803d' : '#4b5563' }};">
                {{ $tableManagement ? 'ON' : 'OFF' }}
            </span>
        </div>

        <div class="mt-4 text-sm text-gray-600">
            Total meja: <strong>{{ $tableCount }}</strong> &middot; Sedang terisi: <strong>{{ $occupiedCount }}</strong>
        </div>

        <form method="POST" action="{{ route('admin.settings.table-management') }}" style="margin-top:1.5rem;">
            @csrf
            @method('PATCH')
            <button type="submit"
                    style="padding:.5rem 1.25rem; border-radius:.5rem; font-size:.875rem; font-weight:600;
                           color:#fff; cursor:pointer; border:0;
                           background: {{ $tableManagement ? '#dc2626' : '#16a34a' }};">
                {{ $tableManagement ? 'Matikan Fitur Meja' : 'Aktifkan Fitur Meja' }}
            </button>
        </form>
    </div>
@endsection