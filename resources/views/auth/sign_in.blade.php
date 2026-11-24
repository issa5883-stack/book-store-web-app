@extends('layouts.auth')

@section('title', __('Sign in'))

@section('side')
    <h2>{{ __('Welcome back to your library.') }}</h2>
    <p>{{ __('Sign in to upload books, follow the status of your requests and build the library together.') }}</p>
    <ul>
        <li><x-icon name="check-circle" /> {{ __('Upload books in PDF format') }}</li>
        <li><x-icon name="check-circle" /> {{ __('Track every request you submit') }}</li>
        <li><x-icon name="check-circle" /> {{ __('Download thousands of books for free') }}</li>
    </ul>
@endsection

@section('content')
    <h1>{{ __('Sign in') }}</h1>
    <p>{{ __('Enter your details to access your account.') }}</p>

    <x-flash />

    <form action="{{ route('login.submit') }}" method="POST">
        @csrf
        <div class="field">
            <label for="email">{{ __('Email address') }}</label>
            <input type="email" id="email" name="email" class="input" value="{{ old('email') }}" placeholder="you@example.com" dir="ltr" required autofocus>
        </div>
        <div class="field">
            <label for="password">{{ __('Password') }}</label>
            <input type="password" id="password" name="password" class="input" placeholder="••••••••" required>
        </div>
        <label class="check"><input type="checkbox" name="remember" value="1"> {{ __('Remember me') }}</label>
        <button type="submit" class="btn btn-primary btn-lg btn-block"><x-icon name="login" /> {{ __('Sign in') }}</button>
    </form>

    <p class="auth-foot">{{ __('No account yet?') }} <a href="{{ route('sign_up') }}"><strong>{{ __('Create one') }}</strong></a></p>
@endsection
