<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    public function definition(): array
    {
        $genres = [
            'Fantasy',
            'Science Fiction',
            'Mystery',
            'Horror',
            'Historical Fiction',
            'Romance',
            'Thriller',
            'Short Story Collection',
            'Programming',
            'Biography',
        ];

        return [
            'title' => fake()->sentence(3),
            'author' => fake()->name(),
            'genre' => fake()->randomElement($genres),
            'published_year' => fake()->numberBetween(1850, 2025),
            'isbn' => fake()->unique()->numerify('978##########'),
            'cover_image' => null,
            'description' => fake()->paragraph(),
            'copies_available' => fake()->numberBetween(0, 50),
        ];
    }
}
