<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function send(Request $request)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:1000',
            ]);

            $message = trim($request->input('message'));
            $lower = strtolower($message);

            // Handle pending confirmation for update/delete
            if (in_array($lower, ['yes', 'confirm', 'proceed', 'okay', 'ok'])) {
                return $this->executePendingAction($request);
            }

            if (in_array($lower, ['no', 'cancel', 'stop'])) {
                $request->session()->forget('pending_action');

                return response()->json([
                    'reply' => 'Okay, I cancelled the pending action.'
                ]);
            }

            // CREATE: "Add a book titled X by Y genre Z year 2000 isbn 123 copies 5"
            if (str_contains($lower, 'add') || str_contains($lower, 'create')) {
                return $this->handleCreate($message);
            }

            // DELETE: "Delete book titled X"
            if (str_contains($lower, 'delete') || str_contains($lower, 'remove')) {
                return $this->handleDelete($request, $message);
            }

            // UPDATE COPIES: "Update copies of X to 20"
            if (str_contains($lower, 'update') || str_contains($lower, 'change')) {
                return $this->handleUpdate($request, $message);
            }

            // SMART FILTERING
            if (str_contains($lower, 'genre') || str_contains($lower, 'horror') || str_contains($lower, 'romance') || str_contains($lower, 'programming') || str_contains($lower, 'science fiction')) {
                return $this->filterByGenre($message);
            }

            if (preg_match('/more than (\d+) copies|greater than (\d+) copies|above (\d+) copies/i', $message, $matches)) {
                $copies = collect($matches)->filter()->last();
                return $this->filterByCopies((int) $copies, 'more');
            }

            if (preg_match('/less than (\d+) copies|under (\d+) copies|below (\d+) copies/i', $message, $matches)) {
                $copies = collect($matches)->filter()->last();
                return $this->filterByCopies((int) $copies, 'less');
            }

            if (preg_match('/published after (\d{4})|after (\d{4})/i', $message, $matches)) {
                $year = collect($matches)->filter()->last();
                return $this->filterByYear((int) $year, 'after');
            }

            if (preg_match('/published before (\d{4})|before (\d{4})/i', $message, $matches)) {
                $year = collect($matches)->filter()->last();
                return $this->filterByYear((int) $year, 'before');
            }

            if (str_contains($lower, 'how many')) {
                return response()->json([
                    'reply' => 'There are currently ' . Book::count() . ' active books in LibAlexandria.'
                ]);
            }

            if (str_contains($lower, 'list') || str_contains($lower, 'show all') || str_contains($lower, 'what books')) {
                return $this->listBooks();
            }

            // Fallback to Gemini with database context
            return $this->askGeminiWithBookContext($message);

        } catch (\Throwable $e) {
            return response()->json([
                'reply' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    private function handleCreate(string $message)
    {
        preg_match('/title(?:d)?\s+["\']([^"\']+)["\']/i', $message, $titleMatch);
        preg_match('/by\s+(.+?)(?:\s+genre|\s+year|\s+isbn|\s+copies|$)/i', $message, $authorMatch);
        preg_match('/genre\s+(.+?)(?:\s+year|\s+isbn|\s+copies|$)/i', $message, $genreMatch);
        preg_match('/(?:year|published year)\s+(\d{4})/i', $message, $yearMatch);
        preg_match('/isbn\s+([0-9\-]+)/i', $message, $isbnMatch);
        preg_match('/copies\s+(\d+)/i', $message, $copiesMatch);

        $title = trim($titleMatch[1] ?? '');
        $author = trim($authorMatch[1] ?? 'Unknown Author');
        $genre = trim($genreMatch[1] ?? 'General');
        $year = (int) ($yearMatch[1] ?? date('Y'));
        $isbn = trim($isbnMatch[1] ?? 'AI-' . time());
        $copies = (int) ($copiesMatch[1] ?? 1);

        if (!$title) {
            return response()->json([
                'reply' => 'Please include a title. Example: Add a book titled "Sample Book" by John Doe genre Fiction year 2020 isbn 12345 copies 5.'
            ]);
        }

        if (Book::where('isbn', $isbn)->exists()) {
            return response()->json([
                'reply' => "I could not create the book because the ISBN {$isbn} already exists."
            ]);
        }

        $book = Book::create([
            'title' => $title,
            'author' => $author,
            'genre' => $genre,
            'published_year' => $year,
            'isbn' => $isbn,
            'description' => 'Created through AI Assistant.',
            'copies_available' => $copies,
            'cover_image' => null,
        ]);

        return response()->json([
            'reply' => "Created book: {$book->title} by {$book->author}. Refreshing the page will show it in the list."
        ]);
    }

    private function handleDelete(Request $request, string $message)
    {
        $title = $this->extractTitle($message);

        if (!$title) {
            return response()->json([
                'reply' => 'Please specify the book title to delete. Example: Delete book titled "The King in Yellow".'
            ]);
        }

        $book = Book::where('title', 'ILIKE', "%{$title}%")->first();

        if (!$book) {
            return response()->json([
                'reply' => "I could not find a book matching \"{$title}\"."
            ]);
        }

        $request->session()->put('pending_action', [
            'type' => 'delete',
            'book_id' => $book->id,
        ]);

        return response()->json([
            'reply' => "Are you sure you want to move \"{$book->title}\" to trash? Reply YES to confirm or NO to cancel."
        ]);
    }

    private function handleUpdate(Request $request, string $message)
    {
        $title = $this->extractTitle($message);

        preg_match('/(?:copies|copy|copies_available).*?(?:to|=)\s*(\d+)|to\s*(\d+)/i', $message, $copyMatch);
        $newCopies = $copyMatch[1] ?? $copyMatch[2] ?? null;

        if (!$title || $newCopies === null) {
            return response()->json([
                'reply' => 'For now, I can update copies. Example: Update copies of "The King in Yellow" to 10.'
            ]);
        }

        $book = Book::where('title', 'ILIKE', "%{$title}%")->first();

        if (!$book) {
            return response()->json([
                'reply' => "I could not find a book matching \"{$title}\"."
            ]);
        }

        $request->session()->put('pending_action', [
            'type' => 'update_copies',
            'book_id' => $book->id,
            'copies_available' => (int) $newCopies,
        ]);

        return response()->json([
            'reply' => "Confirm update: change copies of \"{$book->title}\" from {$book->copies_available} to {$newCopies}? Reply YES to confirm or NO to cancel."
        ]);
    }

    private function executePendingAction(Request $request)
    {
        $pending = $request->session()->get('pending_action');

        if (!$pending) {
            return response()->json([
                'reply' => 'There is no pending action to confirm.'
            ]);
        }

        $book = Book::find($pending['book_id']);

        if (!$book) {
            $request->session()->forget('pending_action');

            return response()->json([
                'reply' => 'The target book could not be found anymore.'
            ]);
        }

        if ($pending['type'] === 'delete') {
            $book->delete();
            $request->session()->forget('pending_action');

            return response()->json([
                'reply' => "Moved \"{$book->title}\" to trash successfully. Refresh the page to see the updated list."
            ]);
        }

        if ($pending['type'] === 'update_copies') {
            $book->update([
                'copies_available' => $pending['copies_available']
            ]);

            $request->session()->forget('pending_action');

            return response()->json([
                'reply' => "Updated \"{$book->title}\" copies to {$book->copies_available}. Refresh the page to see the change."
            ]);
        }

        $request->session()->forget('pending_action');

        return response()->json([
            'reply' => 'Unknown pending action cancelled.'
        ]);
    }

    private function filterByGenre(string $message)
    {
        $genres = ['horror', 'romance', 'programming', 'science fiction', 'short story collection', 'thriller', 'fantasy', 'biography', 'mystery'];

        $foundGenre = null;

        foreach ($genres as $genre) {
            if (str_contains(strtolower($message), $genre)) {
                $foundGenre = $genre;
                break;
            }
        }

        if (!$foundGenre && preg_match('/genre ([a-zA-Z ]+)/i', $message, $match)) {
            $foundGenre = trim($match[1]);
        }

        if (!$foundGenre) {
            return response()->json([
                'reply' => 'Please specify a genre, such as Horror, Romance, Programming, or Science Fiction.'
            ]);
        }

        $books = Book::where('genre', 'ILIKE', "%{$foundGenre}%")->get();

        if ($books->isEmpty()) {
            return response()->json([
                'reply' => "No active books found under genre \"{$foundGenre}\"."
            ]);
        }

        return response()->json([
            'reply' => "Books under {$foundGenre}:\n" . $this->formatBookList($books)
        ]);
    }

    private function filterByCopies(int $copies, string $mode)
    {
        $query = $mode === 'more'
            ? Book::where('copies_available', '>', $copies)
            : Book::where('copies_available', '<', $copies);

        $books = $query->get();

        if ($books->isEmpty()) {
            return response()->json([
                'reply' => "No books found with {$mode} than {$copies} copies."
            ]);
        }

        return response()->json([
            'reply' => "Books with {$mode} than {$copies} copies:\n" . $this->formatBookList($books)
        ]);
    }

    private function filterByYear(int $year, string $mode)
    {
        $query = $mode === 'after'
            ? Book::where('published_year', '>', $year)
            : Book::where('published_year', '<', $year);

        $books = $query->get();

        if ($books->isEmpty()) {
            return response()->json([
                'reply' => "No books found published {$mode} {$year}."
            ]);
        }

        return response()->json([
            'reply' => "Books published {$mode} {$year}:\n" . $this->formatBookList($books)
        ]);
    }

    private function listBooks()
    {
        $books = Book::latest()->take(10)->get();

        if ($books->isEmpty()) {
            return response()->json([
                'reply' => 'There are no active books in the system.'
            ]);
        }

        return response()->json([
            'reply' => "Here are some active books:\n" . $this->formatBookList($books)
        ]);
    }

    private function askGeminiWithBookContext(string $message)
    {
        $apiKey = env('GEMINI_API_KEY');
        $model = env('GEMINI_MODEL', 'gemini-2.5-flash');

        $books = Book::latest()->take(20)->get();

        $bookContext = $books->map(function ($book) {
            return "- {$book->title} by {$book->author}; Genre: {$book->genre}; Year: {$book->published_year}; ISBN: {$book->isbn}; Copies: {$book->copies_available}";
        })->implode("\n");

        $prompt = <<<PROMPT
You are LibAlexandria Assistant for a Laravel book management app.

Use the book data below when answering questions. If the user asks about app features, explain that LibAlexandria manages books with CRUD, cover uploads, soft delete, restore, permanent delete, seeding, and AI chat.

Current active books:
{$bookContext}

User question:
{$message}

Answer briefly and clearly.
PROMPT;

        $response = Http::withoutVerifying()->timeout(30)->post(
            "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}",
            [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]
        );

        if (!$response->successful()) {
            return response()->json([
                'reply' => 'Gemini API error: ' . $response->body()
            ], 500);
        }

        return response()->json([
            'reply' => $response->json('candidates.0.content.parts.0.text') ?? 'No response generated.'
        ]);
    }

    private function extractTitle(string $message): ?string
    {
        if (preg_match('/["\']([^"\']+)["\']/', $message, $match)) {
            return trim($match[1]);
        }

        if (preg_match('/(?:titled|title|of) ([a-zA-Z0-9 .:\-]+)/i', $message, $match)) {
            return trim($match[1]);
        }

        return null;
    }

    private function formatBookList($books): string
    {
        return $books->map(function ($book) {
            return "• {$book->title} by {$book->author} ({$book->genre}, {$book->published_year}) — {$book->copies_available} copies";
        })->implode("\n");
    }
}