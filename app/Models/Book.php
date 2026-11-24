<?php

namespace App\Models;

use Database\Factories\BookFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'title',
    'author',
    'description',
    'category_id',
    'user_id',
    'admin_id',
    'language',
    'published_year',
    'pages',
    'cover',
    'pdf',
    'status',
    'rejection_reason',
    'reviewed_at',
])]
class Book extends Model
{
    /** @use HasFactory<BookFactory> */
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
            'user_id' => 'integer',
            'category_id' => 'integer',
            'published_year' => 'integer',
            'pages' => 'integer',
            'downloads' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * The regular user who submitted the book (null when uploaded by an admin).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The admin who uploaded or reviewed the book.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Only books that were approved by an admin and are visible to the public.
     *
     * @param  Builder<Book>  $query
     */
    public function scopeApproved(Builder $query): void
    {
        $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * @param  Builder<Book>  $query
     */
    public function scopePending(Builder $query): void
    {
        $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Filter by a search term on title / author / category name and an optional category.
     *
     * @param  Builder<Book>  $query
     */
    public function scopeFilter(Builder $query, ?string $search, ?string $categoryId = null): void
    {
        $query
            ->when($search, function (Builder $query, string $search) {
                $query->where(function (Builder $query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('author', 'like', "%{$search}%")
                        ->orWhereHas('category', fn (Builder $query) => $query->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($categoryId, fn (Builder $query, string $categoryId) => $query->where('category_id', $categoryId));
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function coverUrl(): ?string
    {
        return $this->cover ? Storage::disk('public')->url($this->cover) : null;
    }

    public function pdfUrl(): string
    {
        return Storage::disk('public')->url($this->pdf);
    }

    /**
     * Who uploaded the book, for display.
     */
    public function uploaderName(): string
    {
        if ($this->user) {
            return $this->user->name;
        }

        return $this->admin ? $this->admin->name.' ('.__('Admin').')' : __('Admin');
    }

    /**
     * Delete the stored files of this book from the public disk.
     */
    public function deleteFiles(): void
    {
        $paths = collect([$this->cover, $this->pdf])
            ->filter()
            ->reject(fn (string $path) => self::where('id', '!=', $this->id)
                ->where(fn (Builder $query) => $query->where('cover', $path)->orWhere('pdf', $path))
                ->exists());

        Storage::disk('public')->delete($paths->all());
    }

    /**
     * A stable color pair used to draw a cover when no image was uploaded.
     *
     * @return array{0: string, 1: string}
     */
    public function coverColors(): array
    {
        $palettes = [
            ['#1e3a5f', '#7da7d9'], ['#0f5e57', '#6fcfc0'], ['#8a4a0b', '#e9b960'],
            ['#9f1d35', '#f2a0ad'], ['#1d4ed8', '#93c5fd'], ['#7c2d12', '#f0a77a'],
            ['#1f6b3a', '#8fd1a6'], ['#334155', '#94a3b8'],
        ];

        return $palettes[crc32($this->title) % count($palettes)];
    }
}
