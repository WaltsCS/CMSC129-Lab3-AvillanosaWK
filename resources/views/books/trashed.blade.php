@extends('layouts.app')

@section('title', 'Trashed Books')

@section('content')
    <h1>Trashed Books</h1>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <div class="alert-warning">
        These books are in trash. You can restore them or permanently delete them.
    </div>

    <div class="top-links">
        <a href="{{ route('books.index') }}" class="secondary">Back to Active Books</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Deleted At</th>
                <th>Actions</th>
                <th>Cover</th>
            </tr>
        </thead>
        <tbody>
            @forelse($books as $book)
                <tr>
                    <td>{{ $book->title }}</td>
                    <td>{{ $book->author }}</td>
                    <td>{{ $book->deleted_at }}</td>
                    <td>
                        <div class="actions-inline">
                            <form action="{{ route('books.restore', $book->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="success">Restore</button>
                            </form>

                            <form id="force-delete-form-{{ $book->id }}" action="{{ route('books.forceDelete', $book->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="button"
                                    class="danger"
                                    onclick="openConfirmModal(
                                        'force-delete-form-{{ $book->id }}',
                                        'Delete Book Permanently',
                                        'Are you sure you want to permanently delete &quot;{{ addslashes($book->title) }}&quot;? This cannot be undone.',
                                        'Delete Permanently'
                                    )"
                                >
                                    Delete Permanently
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
                    <td colspan="5">Trash is empty.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection