<div class="form-group">
    <label for="title">Title</label>
    <input type="text" id="title" name="title" value="{{ old('title', $book->title ?? '') }}">
    @error('title') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label for="author">Author</label>
    <input type="text" id="author" name="author" value="{{ old('author', $book->author ?? '') }}">
    @error('author') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label for="genre">Genre</label>
    <input type="text" id="genre" name="genre" value="{{ old('genre', $book->genre ?? '') }}">
    @error('genre') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label for="published_year">Published Year</label>
    <input type="number" id="published_year" name="published_year" value="{{ old('published_year', $book->published_year ?? '') }}">
    @error('published_year') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label for="isbn">ISBN</label>
    <input type="text" id="isbn" name="isbn" value="{{ old('isbn', $book->isbn ?? '') }}">
    @error('isbn') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label for="description">Description</label>
    <textarea id="description" name="description">{{ old('description', $book->description ?? '') }}</textarea>
    @error('description') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label for="copies_available">Copies Available</label>
    <input type="number" id="copies_available" name="copies_available" value="{{ old('copies_available', $book->copies_available ?? 0) }}">
    @error('copies_available') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label for="cover_image">Cover Image</label>
    <input type="file" id="cover_image" name="cover_image">
    @error('cover_image') <div class="error">{{ $message }}</div> @enderror

    @if(!empty($book) && $book->cover_image)
        <p class="muted">Current Cover:</p>
        <img src="{{ asset('storage/' . $book->cover_image) }}" alt="Current Cover" class="cover-thumb">
    @endif
</div>

<button type="submit" class="btn success">Save</button>