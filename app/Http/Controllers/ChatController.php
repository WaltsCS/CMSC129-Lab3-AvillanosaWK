<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Book;

class ChatController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message = $request->input('message');
        $apiKey = env('GEMINI_API_KEY');
        $model = env('GEMINI_MODEL', 'gemini-2.5-flash');

        if (!$apiKey) {
            return response()->json([
                'reply' => 'Gemini API key is not configured.'
            ], 500);
        }

        $response = Http::withoutVerifying()->timeout(30)->post(
            "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}",
            [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => "You are LibAlexandria Assistant, a helpful chatbot for a Laravel book management app. Answer clearly and briefly.\n\nUser: {$message}"
                            ]
                        ]
                    ]
                ]
            ]
        );

        if (!$response->successful()) {
            return response()->json([
                'reply' => 'Gemini request failed: ' . $response->body()
            ], 500);
        }

        $reply = $response->json('candidates.0.content.parts.0.text');

        return response()->json([
            'reply' => $reply ?? 'Sorry, I could not generate a response.'
        ]);

        $books = Book::latest()->take(10)->get();

        $bookContext = $books->map(function ($b) {
            return "{$b->title} by {$b->author} ({$b->genre}, {$b->published_year})";
        })->implode("\n");

        $prompt = "You are an assistant for a book management system.

        Here are some books in the system:
        $bookContext

        User question: $message
        Answer clearly based on the data above.";
    }
}