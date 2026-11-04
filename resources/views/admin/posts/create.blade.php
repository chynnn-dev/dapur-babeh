@extends('layouts.admin')

@section('title', 'Tambah Postingan')

@section('content')
    @include('admin.posts._form', [
        'action' => route('admin.posts.store'),
        'method' => 'POST',
    ])
@endsection