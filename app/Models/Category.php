<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug'])]
class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    /**
     * The category name in the visitor's language (lang/ar.json holds the Arabic names).
     * A category an admin adds later without a translation keeps its own name.
     */
    public function localizedName(): string
    {
        $translated = __($this->name);

        return is_string($translated) ? $translated : $this->name;
    }

    /**
     * All categories sorted alphabetically by their name in the current language, for dropdowns.
     *
     * @return Collection<int, Category>
     */
    public static function sortedByLocalizedName(): Collection
    {
        return static::query()->get()
            ->sortBy(fn (Category $category) => $category->localizedName(), SORT_NATURAL | SORT_FLAG_CASE)
            ->values();
    }
}
