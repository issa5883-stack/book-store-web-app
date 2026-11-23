<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $names = ['Programming', 'Science', 'History', 'Novels', 'Business', 'Self Development', 'Religion', 'Languages'];

        foreach ($names as $name) {
            Category::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
        }
    }
}
