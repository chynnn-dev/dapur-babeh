@extends('layouts.admin')

@section('title', 'Tambah User')

@section('content')
    @include('admin.users._form', [
        'action' => route('admin.users.store'),
        'method' => 'POST',
    ])
@endsection