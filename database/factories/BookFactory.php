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
            'source' => Book::SOURCE_ADMIN,
            'reviewed_at' => now(),
        ];
    }

    /**
     * A book submitted by a regular user (approved unless combined with another state).
     */
    public function fromUser(): static
    {
        return $this->state(fn () => [
            'source' => Book::SOURCE_USER,
            'user_id' => User::factory(),
        ]);
    }

    /**
     * A request submitted by a regular user that is waiting for review.
     */
    public function pending(): static
    {
        return $this->fromUser()->state(fn () => [
            'status' => Book::STATUS_PENDING,
            'reviewed_at' => null,
        ]);
    }

    public function rejected(): static
    {
        return $this->fromUser()->state(fn () => [
            'status' => Book::STATUS_REJECTED,
            'rejection_reason' => 'The file is not readable.',
        ]);
    }
}
