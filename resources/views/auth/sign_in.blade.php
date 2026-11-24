@extends('layouts.auth')

@section('title', __('Sign in'))

@section('content')
    <div class="auth-head">
        <h1>{{ __('Welcome back') }}</h1>
        <p>{{ __('Sign in to continue to your library.') }}</p>
    </div>

    <x-flash :show-errors="false" />

    <form action="{{ route('login.submit') }}" method="POST" data-loading>
        @csrf
        <x-auth-field name="email" type="email" :label="__('Email address')"
                      placeholder="you@example.com" dir="ltr" autocomplete="email" inputmode="email" required autofocus />
        <x-auth-field name="password" type="password" :label="__('Password')"
                      placeholder="••••••••" autocomplete="current-password" required />

        <div class="auth-row">
            <label class="check"><input type="checkbox" name="remember" value="1" @checked(old('remember'))> {{ __('Remember me') }}</label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="auth-link">{{ __('Forgot password?') }}</a>
            @endif
        </div>

        <button type="submit" class="btn btn-primary btn-lg btn-block">
            <span class="btn-spinner" aria-hidden="true"></span>
            <span class="btn-label">{{ __('Sign in') }}</span>
        </button>
    </form>

    <div class="auth-divider"><span>{{ __('or') }}</span></div>

    <p class="auth-switch">{{ __('New to BooksWeb?') }} <a href="{{ route('sign_up') }}" class="auth-link">{{ __('Create an account') }}</a></p>
@endsection
