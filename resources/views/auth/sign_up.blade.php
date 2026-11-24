@extends('layouts.auth')

@section('title', __('Create account'))

@section('content')
    <div class="auth-head">
        <h1>{{ __('Create your account') }}</h1>
        <p>{{ __('Join BooksWeb and build your personal library.') }}</p>
    </div>

    <x-flash :show-errors="false" />

    <form action="{{ route('sign_up.submit') }}" method="POST" data-loading>
        @csrf
        <x-auth-field name="name" :label="__('Full name')"
                      :placeholder="__('Your name')" autocomplete="name" required autofocus />
        <x-auth-field name="email" type="email" :label="__('Email address')"
                      placeholder="you@example.com" dir="ltr" autocomplete="email" inputmode="email" required />
        <x-auth-field name="password" type="password" :label="__('Password')" strength
                      :placeholder="__('Create a password')" autocomplete="new-password" minlength="8" required />
        <x-auth-field name="password_confirmation" type="password" :label="__('Confirm password')" matches="password"
                      :placeholder="__('Repeat your password')" autocomplete="new-password" required />

        <label class="check check-top">
            <input type="checkbox" name="terms" value="1" required @checked(old('terms'))>
            <span>{{ __('I agree to the Terms & Conditions') }}</span>
        </label>

        <button type="submit" class="btn btn-primary btn-lg btn-block">
            <span class="btn-spinner" aria-hidden="true"></span>
            <span class="btn-label">{{ __('Create account') }}</span>
        </button>
    </form>

    <div class="auth-divider"><span>{{ __('or') }}</span></div>

    <p class="auth-switch">{{ __('Already have an account?') }} <a href="{{ route('login') }}" class="auth-link">{{ __('Sign in') }}</a></p>
@endsection
