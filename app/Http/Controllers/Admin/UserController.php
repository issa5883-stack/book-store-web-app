<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::withCount([
            'books',
            'books as approved_books_count' => fn ($query) => $query->approved(),
            'books as pending_books_count' => fn ($query) => $query->pending(),
        ])
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function destroy(User $user): RedirectResponse
    {
        foreach ($user->books as $book) {
            $book->deleteFiles();
        }

        $user->delete(); // the user's books are removed by the cascading foreign key

        return back()->with('success', __('User deleted.'));
    }
}
