<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;

Route::get('/', function () {
    return redirect()->route('books.index');
});

Route::resource('books', BookController::class);

// Soft delete routes
Route::get('books/trashed/all', [BookController::class, 'trashed'])->name('books.trashed');
Route::patch('books/{id}/restore', [BookController::class, 'restore'])->name('books.restore');
Route::delete('books/{id}/force-delete', [BookController::class, 'forceDelete'])->name('books.forceDelete');