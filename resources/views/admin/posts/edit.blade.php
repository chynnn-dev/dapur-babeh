@extends('layouts.admin')

@section('title', 'Edit Postingan')

@section('content')
    @include('admin.posts._form', [
        'action' => route('admin.posts.update', $post),
        'method' => 'PUT',
    ])
@endsection