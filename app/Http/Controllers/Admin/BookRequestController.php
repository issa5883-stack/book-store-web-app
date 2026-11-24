<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Review queue: admins approve or reject books submitted by users.
 */
class BookRequestController extends Controller
{
    public function index(): View
    {
        $requests = Book::pending()->with(['user', 'category'])->oldest()->paginate(10);

        return view('admin.requests.index', compact('requests'));
    }

    public function show(Book $book): View
    {
        $book->load(['user', 'category']);

        return view('admin.requests.show', compact('book'));
    }

    public function approve(Book $book): RedirectResponse
    {
        $book->update([
            'status' => Book::STATUS_APPROVED,
            'admin_id' => session('admin_id'),
            'rejection_reason' => null,
            'reviewed_at' => now(),
        ]);

        return redirect()->route('admin.requests.index')->with('success', __('":title" was approved and is now public.', ['title' => $book->title]));
    }

    public function reject(Request $request, Book $book): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:255',
        ]);

        $book->update([
            'status' => Book::STATUS_REJECTED,
            'admin_id' => session('admin_id'),
            'rejection_reason' => $request->rejection_reason,
            'reviewed_at' => now(),
        ]);

        return redirect()->route('admin.requests.index')->with('success', __('":title" was rejected.', ['title' => $book->title]));
    }
}
