<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Public library: only approved books are visible.
 */
class BookController extends Controller
{
    public function home(): View
    {
        $latestBooks = Book::approved()->with('category')->latest()->take(8)->get();
        $popularBooks = Book::approved()->with('category')->orderByDesc('downloads')->take(4)->get();
        $categories = Category::withCount(['books' => fn ($query) => $query->approved()])
            ->orderByDesc('books_count')
            ->take(8)
            ->get();

        $stats = [
            'books' => Book::approved()->count(),
            'categories' => Category::count(),
            'downloads' => (int) Book::approved()->sum('downloads'),
            'pending' => Book::pending()->count(),
            'members' => User::count(),
        ];

        return view('home', compact('latestBooks', 'popularBooks', 'categories', 'stats'));
    }

    public function index(Request $request): View
    {
        $books = Book::approved()
            ->with('category')
            ->filter($request->search, $request->category)
            ->when($request->sort === 'popular', fn ($query) => $query->orderByDesc('downloads'))
            ->when($request->sort === 'title', fn ($query) => $query->orderBy('title'))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('books.index', compact('books', 'categories'));
    }

    public function show(Book $book): View
    {
        abort_unless($book->isApproved(), 404);

        $book->load(['category', 'user', 'admin']);

        $relatedBooks = Book::approved()
            ->with('category')
            ->where('id', '!=', $book->id)
            ->when($book->category_id, fn ($query) => $query->where('category_id', $book->category_id))
            ->latest()
            ->take(4)
            ->get();

        return view('books.show', compact('book', 'relatedBooks'));
    }

    public function download(Book $book): StreamedResponse
    {
        abort_unless($book->isApproved(), 404);

        $book->increment('downloads');

        return Storage::disk('public')->download($book->pdf, str($book->title)->slug().'.pdf');
    }
}
