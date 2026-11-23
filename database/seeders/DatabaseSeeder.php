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
     * Seed the application's database.
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

        $demoBooks = [
            ['Clean Code', 'Robert C. Martin', 'Programming'],
            ['The Pragmatic Programmer', 'Andrew Hunt', 'Programming'],
            ['A Brief History of Time', 'Stephen Hawking', 'Science'],
            ['Cosmos', 'Carl Sagan', 'Science'],
            ['Sapiens', 'Yuval Noah Harari', 'History'],
            ['The Alchemist', 'Paulo Coelho', 'Novels'],
            ['Atomic Habits', 'James Clear', 'Self Development'],
            ['The Lean Startup', 'Eric Ries', 'Business'],
        ];

        foreach ($demoBooks as [$title, $author, $categoryName]) {
            Book::factory()->create([
                'title' => $title,
                'author' => $author,
                'category_id' => $categories->firstWhere('name', $categoryName)->id,
                'admin_id' => $admin->id,
                'downloads' => fake()->numberBetween(5, 400),
            ]);
        }

        Book::factory()->pending()->create([
            'title' => 'Deep Work',
            'author' => 'Cal Newport',
            'user_id' => $user->id,
            'category_id' => $categories->firstWhere('name', 'Self Development')->id,
        ]);
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
