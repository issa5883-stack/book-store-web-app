@extends('layouts.auth')

@section('title', __('Admin sign in'))

@section('content')
    <div class="auth-head">
        <span class="auth-badge"><x-icon name="shield" /> {{ __('Administrators only') }}</span>
        <h1>{{ __('Admin sign in') }}</h1>
        <p>{{ __('Use your administrator credentials.') }}</p>
    </div>

    <x-flash :show-errors="false" />

    <form action="{{ route('adminlogin.submit') }}" method="POST" data-loading>
        @csrf
        <x-auth-field name="email" type="email" :label="__('Email address')"
                      placeholder="admin@example.com" dir="ltr" autocomplete="username" inputmode="email" required autofocus />
        <x-auth-field name="password" type="password" :label="__('Password')"
                      placeholder="••••••••" autocomplete="current-password" required />

        <button type="submit" class="btn btn-primary btn-lg btn-block">
            <span class="btn-spinner" aria-hidden="true"></span>
            <span class="btn-label">{{ __('Sign in to dashboard') }}</span>
        </button>
    </form>

    <div class="auth-divider"><span>{{ __('or') }}</span></div>

    <p class="auth-switch"><a href="{{ route('home') }}" class="auth-link">{{ __('Back to website') }}</a></p>
@endsection
