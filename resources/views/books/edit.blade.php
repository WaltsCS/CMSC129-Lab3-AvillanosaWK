@extends('layouts.app')

@section('title', 'Edit Book')

@section('content')
    <h1>Edit Book</h1>

    <div class="top-links">
        <a href="{{ route('books.index') }}" class="secondary">Back to List</a>
    </div>

    <form action="{{ route('books.update', $book) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('books.form')
    </form>
@endsection