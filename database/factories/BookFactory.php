<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => rtrim(fake()->sentence(3), '.'),
            'author' => fake()->name(),
            'description' => fake()->paragraphs(2, true),
            'category_id' => Category::factory(),
            'language' => fake()->randomElement(['English', 'Arabic']),
            'published_year' => fake()->numberBetween(1950, (int) date('Y')),
            'pages' => fake()->numberBetween(80, 900),
            'pdf' => 'books/sample.pdf',
            'status' => Book::STATUS_APPROVED,
            'reviewed_at' => now(),
        ];
    }

    /**
     * A request submitted by a regular user that is waiting for review.
     */
    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => Book::STATUS_PENDING,
            'user_id' => User::factory(),
            'reviewed_at' => null,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'status' => Book::STATUS_REJECTED,
            'user_id' => User::factory(),
            'rejection_reason' => 'The file is not readable.',
        ]);
    }
}
