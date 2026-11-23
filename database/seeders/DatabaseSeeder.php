<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database. Safe to run again: demo books are matched by title.
     */
    public function run(): void
    {
        $this->call([AdminSeeder::class, CategorySeeder::class]);

        $user = User::updateOrCreate(
            ['email' => 'user@example.com'],
            ['name' => 'Test User', 'password' => 'User@12345']
        );

        $this->storeSamplePdf();

        $admin = Admin::first();
        $categories = Category::all();

        foreach ($this->demoBooks() as $book) {
            Book::updateOrCreate(['title' => $book['title']], [
                'author' => $book['author'],
                'description' => $book['description'],
                'category_id' => $categories->firstWhere('name', $book['category'])->id,
                'language' => 'English',
                'published_year' => $book['year'],
                'pages' => $book['pages'],
                'pdf' => 'books/sample.pdf',
                'status' => $book['pending'] ?? false ? Book::STATUS_PENDING : Book::STATUS_APPROVED,
                'admin_id' => $book['pending'] ?? false ? null : $admin->id,
                'user_id' => $book['pending'] ?? false ? $user->id : null,
                'reviewed_at' => $book['pending'] ?? false ? null : now(),
            ]);
        }
    }

    /**
     * @return array<int, array{title: string, author: string, category: string, year: int, pages: int, description: string, pending?: bool}>
     */
    private function demoBooks(): array
    {
        return [
            ['title' => 'Clean Code', 'author' => 'Robert C. Martin', 'category' => 'Programming', 'year' => 2008, 'pages' => 464,
                'description' => 'A handbook of habits for writing code other people can read: naming, small functions, comments, error handling and tests, shown through before-and-after examples in Java.'],
            ['title' => 'The Pragmatic Programmer', 'author' => 'Andrew Hunt', 'category' => 'Programming', 'year' => 1999, 'pages' => 352,
                'description' => 'Short, practical chapters on the daily work of a developer, from version control and estimating to keeping code easy to change.'],
            ['title' => 'A Brief History of Time', 'author' => 'Stephen Hawking', 'category' => 'Science', 'year' => 1988, 'pages' => 256,
                'description' => 'The big bang, black holes and the nature of time, explained for readers without a background in physics.'],
            ['title' => 'Cosmos', 'author' => 'Carl Sagan', 'category' => 'Science', 'year' => 1980, 'pages' => 396,
                'description' => 'A tour of the universe and of how science came to understand it, written alongside the television series of the same name.'],
            ['title' => 'Sapiens', 'author' => 'Yuval Noah Harari', 'category' => 'History', 'year' => 2011, 'pages' => 443,
                'description' => 'How Homo sapiens came to rule the world, told through three revolutions: the cognitive, the agricultural and the scientific.'],
            ['title' => 'The Alchemist', 'author' => 'Paulo Coelho', 'category' => 'Novels', 'year' => 1988, 'pages' => 208,
                'description' => 'A shepherd boy leaves Andalusia for the Egyptian desert in search of a treasure he has dreamed about.'],
            ['title' => 'Atomic Habits', 'author' => 'James Clear', 'category' => 'Self Development', 'year' => 2018, 'pages' => 320,
                'description' => 'How small changes in daily routine add up, with a four-step model for building good habits and breaking bad ones.'],
            ['title' => 'The Lean Startup', 'author' => 'Eric Ries', 'category' => 'Business', 'year' => 2011, 'pages' => 336,
                'description' => 'A method for testing a new business idea with small experiments before spending months building it.'],
            ['title' => 'Deep Work', 'author' => 'Cal Newport', 'category' => 'Self Development', 'year' => 2016, 'pages' => 304, 'pending' => true,
                'description' => 'Why long stretches of focused work have become rare and valuable, and rules for making room for them.'],
        ];
    }

    /**
     * Write a tiny valid PDF used by the demo books.
     */
    private function storeSamplePdf(): void
    {
        $pdf = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n"
            ."2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n"
            ."3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 595 842]/Contents 4 0 R/Resources<</Font<</F1 5 0 R>>>>>>endobj\n"
            ."4 0 obj<</Length 58>>stream\nBT /F1 28 Tf 160 760 Td (BooksWeb Sample Book) Tj ET\nendstream endobj\n"
            ."5 0 obj<</Type/Font/Subtype/Type1/BaseFont/Helvetica>>endobj\n"
            ."trailer<</Root 1 0 R>>\n%%EOF";

        Storage::disk('public')->put('books/sample.pdf', $pdf);
    }
}
