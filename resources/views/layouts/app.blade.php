<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ config('app.supported_locales.'.app()->getLocale().'.dir', 'ltr') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('Digital Library')) · {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400..700{{ app()->getLocale() === 'ar' ? '&family=Cairo:wght@400..700' : '' }}&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header class="navbar">
        <div class="container">
            <a href="{{ route('home') }}" class="brand">
                <span class="brand-mark" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor"><circle cx="6.5" cy="6.5" r="4.5"/><circle cx="17.5" cy="6.5" r="4.5"/><circle cx="6.5" cy="17.5" r="4.5"/><rect x="13" y="13" width="9" height="9" rx="1.5"/></svg></span>
                <span>BooksWeb</span>
            </a>

            <nav class="nav-links">
                <a href="{{ route('home') }}" @class(['active' => request()->routeIs('home')])>{{ __('Home') }}</a>
                <a href="{{ route('books.index') }}" @class(['active' => request()->routeIs('books.*')])>{{ __('Browse Books') }}</a>
                @auth
                    <a href="{{ route('my-books.index') }}" @class(['active' => request()->routeIs('my-books.index', 'my-books.edit')])>{{ __('My Books') }}</a>
                    <a href="{{ route('my-books.create') }}" @class(['active' => request()->routeIs('my-books.create')])>{{ __('Submit a Book') }}</a>
                @endauth
            </nav>

            <div class="nav-actions">
                <x-language-switcher class="btn btn-ghost btn-sm" />
                @auth
                    <a href="{{ route('my-books.create') }}" class="btn btn-primary btn-sm"><x-icon name="upload" /> {{ __('Upload') }}</a>
                    <div class="user-chip">
                        <span class="avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                        <span class="name">{{ auth()->user()->name }}</span>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-ghost btn-icon" title="{{ __('Sign out') }}"><x-icon name="logout" /></button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">{{ __('Sign in') }}</a>
                    <a href="{{ route('sign_up') }}" class="btn btn-primary btn-sm">{{ __('Create account') }}</a>
                @endauth
            </div>
        </div>
    </header>

    @hasSection('page-head')
        @yield('page-head')
    @endif

    <main>
        @if (session('success') || session('error'))
            <div class="container" style="padding-top: 1.5rem;">
                <x-flash />
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="footer">
        <div class="container">
            <div>
                <a href="{{ route('home') }}" class="brand">
                    <span class="brand-mark" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor"><circle cx="6.5" cy="6.5" r="4.5"/><circle cx="17.5" cy="6.5" r="4.5"/><circle cx="6.5" cy="17.5" r="4.5"/><rect x="13" y="13" width="9" height="9" rx="1.5"/></svg></span>
                    <span>BooksWeb</span>
                </a>
                <p style="margin-top: 1rem; max-width: 22rem;">{{ __('A community digital library. Read, download and share books — every upload is reviewed by our team.') }}</p>
            </div>
            <div class="footer-links">
                <a href="{{ route('books.index') }}">{{ __('Browse') }}</a>
                @auth
                    <a href="{{ route('my-books.create') }}">{{ __('Submit a book') }}</a>
                @else
                    <a href="{{ route('sign_up') }}">{{ __('Join') }}</a>
                @endauth
                <a href="{{ route('admin.login') }}">{{ __('Admin') }}</a>
                <x-language-switcher />
            </div>
        </div>
        <div class="container footer-bottom">© {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}</div>
    </footer>

    @include('partials.scripts')
</body>
</html>
