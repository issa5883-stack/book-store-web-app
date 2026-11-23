<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Sign up / sign in for regular users (default "web" guard).
 */
class AuthController extends Controller
{
    public function showSignUp(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('my-books.index');
        }

        return view('auth.sign_up');
    }

    public function signUp(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create($data); // password is hashed by the model cast

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('my-books.index')->with('success', 'Welcome '.$user->name.'! Your account is ready.');
    }

    public function showSignIn(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('my-books.index');
        }

        return view('auth.sign_in');
    }

    public function signIn(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('my-books.index'));
        }

        return back()->withErrors(['email' => 'Email or password is wrong.'])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
