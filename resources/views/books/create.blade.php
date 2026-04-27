@extends('layouts.app')

@section('title', 'Add Book')

@section('content')
    <h1>Add New Book</h1>

    <div class="top-links">
        <a href="{{ route('books.index') }}" class="secondary">Back to List</a>
    </div>

    <form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('books.form')
    </form>
@endsection