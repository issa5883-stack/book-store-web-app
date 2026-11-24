<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Admin login (separate "admins" table, stored in session as admin_id) and dashboard.
 */
class AdminController extends Controller
{
    public function loginform(): View|RedirectResponse
    {
        if (session()->has('admin_id')) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if ($admin && Hash::check($request->password, $admin->password)) {
            $request->session()->regenerate();
            session(['admin_id' => $admin->id]);

            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => __('Email or password is wrong.')])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        session()->forget('admin_id');
        $request->session()->regenerate();

        return redirect()->route('admin.login')->with('success', __('You have been signed out.'));
    }

    public function dashboard(): View
    {
        $stats = [
            'books' => Book::approved()->count(),
            'pending' => Book::pending()->count(),
            'users' => User::count(),
            'categories' => Category::count(),
            'downloads' => (int) Book::sum('downloads'),
        ];

        $pendingBooks = Book::pending()->with(['user', 'category'])->latest()->take(5)->get();
        $latestBooks = Book::approved()->with('category')->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'pendingBooks', 'latestBooks'));
    }
}
