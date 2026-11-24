<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Regular users submit books as requests. Every request starts as "pending"
 * and becomes public only after an admin approves it.
 */
class UserBookController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $books = $user->books()
            ->with('category')
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $counts = $user->books()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('my-books.index', compact('books', 'counts'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('my-books.create', compact('categories'));
    }

    public function store(BookRequest $request): RedirectResponse
    {
        $data = $this->bookDataWithFiles($request);
        $data['status'] = Book::STATUS_PENDING;

        $request->user()->books()->create($data);

        return redirect()->route('my-books.index')
            ->with('success', __('Your book was submitted and is waiting for admin approval.'));
    }

    public function edit(Request $request, Book $book): View
    {
        $this->ensureEditable($request, $book);

        $categories = Category::orderBy('name')->get();

        return view('my-books.edit', compact('book', 'categories'));
    }

    public function update(BookRequest $request, Book $book): RedirectResponse
    {
        $this->ensureEditable($request, $book);

        $data = $this->bookDataWithFiles($request, $book);
        $data['status'] = Book::STATUS_PENDING; // edited requests go back to review
        $data['rejection_reason'] = null;
        $data['reviewed_at'] = null;

        $book->update($data);

        return redirect()->route('my-books.index')
            ->with('success', __('Your request was updated and sent for review again.'));
    }

    public function destroy(Request $request, Book $book): RedirectResponse
    {
        abort_unless($book->user_id === $request->user()->id, 403);

        $book->deleteFiles();
        $book->delete();

        return redirect()->route('my-books.index')->with('success', __('Book deleted.'));
    }

    /**
     * Users may edit only their own requests, and only before they are approved.
     */
    private function ensureEditable(Request $request, Book $book): void
    {
        abort_unless($book->user_id === $request->user()->id, 403);
        abort_if($book->isApproved(), 403, __('Approved books can only be changed by an admin.'));
    }
}
