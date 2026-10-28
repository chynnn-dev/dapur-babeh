@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
    @include('admin.users._form', [
        'action' => route('admin.users.update', $user),
        'method' => 'PUT',
    ])
@endsection