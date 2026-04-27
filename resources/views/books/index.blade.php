@extends('layouts.app')

@section('title', 'LibAlexandria - Books')

@section('content')
    <h1>Books List</h1>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <div class="top-links">
        <a href="{{ route('books.create') }}">Add New Book</a>
        <a href="{{ route('books.trashed') }}" class="secondary">View Trash</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Genre</th>
                <th>Year</th>
                <th>ISBN</th>
                <th>Copies</th>
                <th>Actions</th>
                <th>Cover</th>
            </tr>
        </thead>
        <tbody>
            @forelse($books as $book)
                <tr>
                    <td>{{ $book->title }}</td>
                    <td>{{ $book->author }}</td>
                    <td>{{ $book->genre }}</td>
                    <td>{{ $book->published_year }}</td>
                    <td>{{ $book->isbn }}</td>
                    <td>{{ $book->copies_available }}</td>
                    <td>
                        <div class="actions-inline">
                            <a href="{{ route('books.show', $book) }}" class="btn">View</a>
                            <a href="{{ route('books.edit', $book) }}" class="btn secondary">Edit</a>

                            <form id="trash-form-{{ $book->id }}" action="{{ route('books.destroy', $book) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="button"
                                    class="warning"
                                    onclick="openConfirmModal(
                                        'trash-form-{{ $book->id }}',
                                        'Move Book to Trash',
                                        'Are you sure you want to move &quot;{{ addslashes($book->title) }}&quot; to trash?',
                                        'Move to Trash'
                                    )"
                                >
                                    Trash
                                </button>
                            </form>
                        </div>
                    </td>
                    <td>
                        @if($book->cover_image)
                            <img src="{{ asset('storage/' . $book->cover_image) }}" alt="Book Cover" class="cover-thumb">
                        @else
                            <span class="muted">No cover</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">No books found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection