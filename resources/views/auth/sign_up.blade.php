@extends('layouts.auth')

@section('title', 'Create account')

@section('side')
    <h2>Share the books you love.</h2>
    <p>Create a free account and submit books to the library. Our admins review every request before it goes public.</p>
    <ul>
        <li><x-icon name="upload" /> 1. Upload your book</li>
        <li><x-icon name="clock" /> 2. An admin reviews it</li>
        <li><x-icon name="globe" /> 3. It's published for everyone</li>
    </ul>
@endsection

@section('content')
    <h1>Create account</h1>
    <p>It only takes a minute.</p>

    <form action="{{ route('sign_up.submit') }}" method="POST">
        @csrf
        <div class="field">
            <label for="name">Full name</label>
            <input id="name" name="name" class="input @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Your name" required autofocus>
            @error('name') <div class="error-text">{{ $message }}</div> @enderror
        </div>
        <div class="field">
            <label for="email">Email address</label>
            <input type="email" id="email" name="email" class="input @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="you@example.com" required>
            @error('email') <div class="error-text">{{ $message }}</div> @enderror
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="input @error('password') is-invalid @enderror" placeholder="At least 8 characters" required>
            @error('password') <div class="error-text">{{ $message }}</div> @enderror
        </div>
        <div class="field">
            <label for="password_confirmation">Confirm password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="input" placeholder="Repeat your password" required>
        </div>
        <button type="submit" class="btn btn-primary btn-lg btn-block">Create account <x-icon name="arrow-right" /></button>
    </form>

    <p class="auth-foot">Already have an account? <a href="{{ route('login') }}"><strong>Sign in</strong></a></p>
@endsection
