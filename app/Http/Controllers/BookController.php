<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
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
        // Popular / latest show the library's own books; user submissions get their own section.
        $popularBooks = Book::approved()->fromLibrary()->with('category')->orderByDesc('downloads')->take(5)->get();
        $latestBooks = Book::approved()->fromLibrary()->with('category')->latest()->take(10)->get();
        $userBooks = Book::approved()->fromUsers()->with('category')->latest()->take(5)->get();

        $categories = Category::withCount(['books' => fn ($query) => $query->approved()])
            ->orderByDesc('books_count')
            ->orderBy('name')
            ->get();

        // Authors are a column on books, so they are ranked by how often their approved books are downloaded.
        $popularAuthors = Book::approved()
            ->selectRaw('author, count(*) as books_count, sum(downloads) as total_downloads')
            ->groupBy('author')
            ->orderByDesc('total_downloads')
            ->take(8)
            ->get();

        $stats = [
            'books' => Book::approved()->count(),
            'categories' => Category::count(),
        ];

        return view('home', compact('popularBooks', 'latestBooks', 'userBooks', 'categories', 'popularAuthors', 'stats'));
    }

    public function index(Request $request): View
    {
        $source = in_array($request->source, Book::SOURCES, true) ? $request->source : null;

        $books = Book::approved()
            ->with('category')
            ->filter($request->search, $request->category)
            ->when($source, fn ($query, string $source) => $query->where('source', $source))
            ->when($request->sort === 'popular', fn ($query) => $query->orderByDesc('downloads'))
            ->when($request->sort === 'title', fn ($query) => $query->orderBy('title'))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = Category::sortedByLocalizedName();

        return view('books.index', compact('books', 'categories', 'source'));
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

    /**
     * Stream the PDF inline so signed-in users can read it in the browser.
     */
    public function read(Book $book): StreamedResponse
    {
        abort_unless($book->isApproved(), 404);

        return Storage::disk('public')->response($book->pdf, str($book->title)->slug().'.pdf');
    }

    public function download(Book $book): StreamedResponse
    {
        abort_unless($book->isApproved(), 404);

        $book->increment('downloads');

        return Storage::disk('public')->download($book->pdf, str($book->title)->slug().'.pdf');
    }
}
