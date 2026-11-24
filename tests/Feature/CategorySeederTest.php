<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategorySeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_all_library_categories_without_duplicates(): void
    {
        $this->seed(CategorySeeder::class);
        $this->seed(CategorySeeder::class);

        $this->assertSame(20, Category::count());
        $this->assertEqualsCanonicalizing(CategorySeeder::NAMES, Category::pluck('name')->all());
    }

    public function test_the_old_languages_category_is_renamed_and_keeps_its_books(): void
    {
        $languages = Category::create(['name' => 'Languages', 'slug' => 'languages']);
        $book = Book::factory()->for($languages)->create();

        $this->seed(CategorySeeder::class);

        $this->assertSame('Language', $languages->refresh()->name);
        $this->assertSame($languages->id, $book->refresh()->category_id);
        $this->assertSame(20, Category::count());
    }

    public function test_category_names_are_shown_in_arabic(): void
    {
        $this->seed(CategorySeeder::class);
        $psychology = Category::where('slug', 'psychology')->sole();

        $this->assertSame('Psychology', $psychology->localizedName());

        app()->setLocale('ar');
        $this->assertSame('علم النفس', $psychology->localizedName());

        $this->withSession(['locale' => 'ar'])->get(route('home'))->assertSee('علم النفس')->assertSee('البرمجة');
    }

    public function test_a_category_without_translation_keeps_its_name(): void
    {
        $category = Category::factory()->create(['name' => 'Gardening']);
        app()->setLocale('ar');

        $this->assertSame('Gardening', $category->localizedName());
    }
}
