@extends('layouts.auth')

@section('title', 'Sign in')

@section('side')
    <h2>Welcome back to your library.</h2>
    <p>Sign in to upload books, follow the status of your requests and build the library together.</p>
    <ul>
        <li><x-icon name="check-circle" /> Upload books in PDF format</li>
        <li><x-icon name="check-circle" /> Track every request you submit</li>
        <li><x-icon name="check-circle" /> Download thousands of books for free</li>
    </ul>
@endsection

@section('content')
    <h1>Sign in</h1>
    <p>Enter your details to access your account.</p>

    <x-flash />

    <form action="{{ route('login.submit') }}" method="POST">
        @csrf
        <div class="field">
            <label for="email">Email address</label>
            <input type="email" id="email" name="email" class="input" value="{{ old('email') }}" placeholder="you@example.com" required autofocus>
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="input" placeholder="••••••••" required>
        </div>
        <label class="check"><input type="checkbox" name="remember" value="1"> Remember me</label>
        <button type="submit" class="btn btn-primary btn-lg btn-block"><x-icon name="login" /> Sign in</button>
    </form>

    <p class="auth-foot">No account yet? <a href="{{ route('sign_up') }}"><strong>Create one</strong></a></p>
    <p class="auth-foot" style="margin-top: .5rem;"><a href="{{ route('admin.login') }}" class="muted"><x-icon name="shield" /> Administrator sign in</a></p>
@endsection
