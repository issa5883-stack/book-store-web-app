<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookRequest;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Full book management for admins. Books uploaded by an admin are published immediately.
 */
class AdminBookController extends Controller
{
    public function index(Request $request): View
    {
        $books = Book::with(['category', 'user', 'admin'])
            ->filter($request->search, $request->category)
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $categories = Category::sortedByLocalizedName();

        return view('admin.books.index', compact('books', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::sortedByLocalizedName();

        return view('admin.books.create', compact('categories'));
    }

    public function store(BookRequest $request): RedirectResponse
    {
        $data = $this->bookDataWithFiles($request);
        $data['status'] = Book::STATUS_APPROVED;
        $data['source'] = Book::SOURCE_ADMIN;
        $data['admin_id'] = session('admin_id');
        $data['reviewed_at'] = now();

        Book::create($data);

        return redirect()->route('admin.books.index')->with('success', __('Book published successfully.'));
    }

    public function edit(Book $book): View
    {
        $categories = Category::sortedByLocalizedName();

        return view('admin.books.edit', compact('book', 'categories'));
    }

    public function update(BookRequest $request, Book $book): RedirectResponse
    {
        $book->update($this->bookDataWithFiles($request, $book));

        return redirect()->route('admin.books.index')->with('success', __('Book updated.'));
    }

    public function destroy(Book $book): RedirectResponse
    {
        $book->deleteFiles();
        $book->delete();

        return back()->with('success', __('Book deleted.'));
    }
}
