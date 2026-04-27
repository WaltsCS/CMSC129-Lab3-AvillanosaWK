@extends('layouts.app')

@section('title', 'View Book')

@section('content')
    <h1>Book Details</h1>

    <div class="top-links">
        <a href="{{ route('books.index') }}" class="secondary">Back to List</a>
        <a href="{{ route('books.edit', $book) }}">Edit Book</a>
    </div>

    <div class="details">
        <p><strong>Title:</strong> {{ $book->title }}</p>
        <p><strong>Author:</strong> {{ $book->author }}</p>
        <p><strong>Genre:</strong> {{ $book->genre }}</p>
        <p><strong>Published Year:</strong> {{ $book->published_year }}</p>
        <p><strong>ISBN:</strong> {{ $book->isbn }}</p>
        <p><strong>Description:</strong> {{ $book->description ?: 'No description available.' }}</p>
        <p><strong>Copies Available:</strong> {{ $book->copies_available }}</p>

        @if($book->cover_image)
            <p><strong>Cover:</strong></p>
            <img src="{{ asset('storage/' . $book->cover_image) }}" alt="Book Cover" class="cover-large">
        @endif
    </div>
@endsection