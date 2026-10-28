@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl bg-white p-6 shadow">
            <div class="text-sm text-gray-500">Total User</div>
            <div class="mt-1 text-3xl font-bold">{{ $totalUsers }}</div>
        </div>

        @foreach (\App\Models\User::ROLES as $key => $label)
            <div class="rounded-xl bg-white p-6 shadow">
                <div class="text-sm text-gray-500">{{ $label }}</div>
                <div class="mt-1 text-3xl font-bold">{{ $roleCounts[$key] ?? 0 }}</div>
            </div>
        @endforeach
    </div>
@endsection