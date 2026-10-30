@extends('layouts.admin')

@section('title', 'Edit Menu')

@section('content')
    @include('admin.menus._form', [
        'action' => route('admin.menus.update', $menu),
        'method' => 'PUT',
    ])
@endsection