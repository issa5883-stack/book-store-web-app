@extends('layouts.auth')

@section('title', __('Create account'))

@section('side')
    <h2>{{ __('Share the books you love.') }}</h2>
    <p>{{ __('Create a free account and submit books to the library. Our admins review every request before it goes public.') }}</p>
    <ul>
        <li><x-icon name="upload" /> {{ __('1. Upload your book') }}</li>
        <li><x-icon name="clock" /> {{ __('2. An admin reviews it') }}</li>
        <li><x-icon name="globe" /> {{ __("3. It's published for everyone") }}</li>
    </ul>
@endsection

@section('content')
    <h1>{{ __('Create account') }}</h1>
    <p>{{ __('It only takes a minute.') }}</p>

    <form action="{{ route('sign_up.submit') }}" method="POST">
        @csrf
        <div class="field">
            <label for="name">{{ __('Full name') }}</label>
            <input id="name" name="name" class="input @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="{{ __('Your name') }}" required autofocus>
            @error('name') <div class="error-text">{{ $message }}</div> @enderror
        </div>
        <div class="field">
            <label for="email">{{ __('Email address') }}</label>
            <input type="email" id="email" name="email" class="input @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="you@example.com" dir="ltr" required>
            @error('email') <div class="error-text">{{ $message }}</div> @enderror
        </div>
        <div class="field">
            <label for="password">{{ __('Password') }}</label>
            <input type="password" id="password" name="password" class="input @error('password') is-invalid @enderror" placeholder="{{ __('At least 8 characters') }}" required>
            @error('password') <div class="error-text">{{ $message }}</div> @enderror
        </div>
        <div class="field">
            <label for="password_confirmation">{{ __('Confirm password') }}</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="input" placeholder="{{ __('Repeat your password') }}" required>
        </div>
        <button type="submit" class="btn btn-primary btn-lg btn-block">{{ __('Create account') }} <x-icon name="arrow-right" class="flip-rtl" /></button>
    </form>

    <p class="auth-foot">{{ __('Already have an account?') }} <a href="{{ route('login') }}"><strong>{{ __('Sign in') }}</strong></a></p>
@endsection
