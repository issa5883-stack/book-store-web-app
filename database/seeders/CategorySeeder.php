<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * The library's subjects (what a book is about). Arabic names live in lang/ar.json.
     *
     * @var list<string>
     */
    public const NAMES = [
        'Programming', 'Science', 'Technology', 'History', 'Novels',
        'Literature', 'Philosophy', 'Religion', 'Psychology', 'Self Development',
        'Business', 'Economics', 'Education', 'Biography', 'Poetry',
        'Children', 'Arts', 'Health', 'Language', 'Law',
    ];

    public function run(): void
    {
        // The earlier "Languages" category becomes "Language", keeping its id and books.
        if (! Category::where('slug', 'language')->exists()) {
            Category::where('slug', 'languages')->update(['name' => 'Language', 'slug' => 'language']);
        }

        foreach (self::NAMES as $name) {
            Category::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
        }
    }
}
