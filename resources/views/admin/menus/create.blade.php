@extends('layouts.admin')

@section('title', 'Tambah Menu')

@section('content')
    @include('admin.menus._form', [
        'action' => route('admin.menus.store'),
        'method' => 'POST',
    ])
@endsection