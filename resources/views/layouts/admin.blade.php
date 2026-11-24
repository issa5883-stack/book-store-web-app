<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ config('app.supported_locales.'.app()->getLocale().'.dir', 'ltr') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('Dashboard')) · {{ __('Admin') }} · {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400..700{{ app()->getLocale() === 'ar' ? '&family=Cairo:wght@400..700' : '' }}&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    @php($pendingCount = \App\Models\Book::pending()->count())
    <div class="admin-shell">
        <aside class="sidebar">
            <a href="{{ route('admin.dashboard') }}" class="brand">
                <span class="brand-mark" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor"><circle cx="6.5" cy="6.5" r="4.5"/><circle cx="17.5" cy="6.5" r="4.5"/><circle cx="6.5" cy="17.5" r="4.5"/><rect x="13" y="13" width="9" height="9" rx="1.5"/></svg></span>
                <span>BooksWeb</span>
            </a>

            <div class="side-label">{{ __('Overview') }}</div>
            <nav class="side-nav">
                <a href="{{ route('admin.dashboard') }}" @class(['active' => request()->routeIs('admin.dashboard')])><x-icon name="grid" /> {{ __('Dashboard') }}</a>
                <a href="{{ route('admin.requests.index') }}" @class(['active' => request()->routeIs('admin.requests.*')])>
                    <x-icon name="inbox" /> {{ __('Book requests') }}
                    @if ($pendingCount)
                        <span class="count-pill">{{ $pendingCount }}</span>
                    @endif
                </a>
            </nav>

            <div class="side-label">{{ __('Library') }}</div>
            <nav class="side-nav">
                <a href="{{ route('admin.books.index') }}" @class(['active' => request()->routeIs('admin.books.index', 'admin.books.edit')])><x-icon name="book" /> {{ __('All books') }}</a>
                <a href="{{ route('admin.books.create') }}" @class(['active' => request()->routeIs('admin.books.create')])><x-icon name="upload" /> {{ __('Upload a book') }}</a>
                <a href="{{ route('admin.categories.index') }}" @class(['active' => request()->routeIs('admin.categories.*')])><x-icon name="tag" /> {{ __('Categories') }}</a>
                <a href="{{ route('admin.users.index') }}" @class(['active' => request()->routeIs('admin.users.*')])><x-icon name="users" /> {{ __('Users') }}</a>
            </nav>

            <div class="side-label">{{ __('Other') }}</div>
            <nav class="side-nav">
                <a href="{{ route('home') }}" target="_blank"><x-icon name="external" /> {{ __('View website') }}</a>
                <x-language-switcher />
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit"><x-icon name="logout" /> {{ __('Sign out') }}</button>
                </form>
            </nav>

            <div class="side-foot">
                <span class="avatar">{{ mb_substr($currentAdmin->name, 0, 1) }}</span>
                <div>
                    <strong>{{ $currentAdmin->name }}</strong>
                    <span>{{ $currentAdmin->email }}</span>
                </div>
            </div>
        </aside>

        <div class="admin-main">
            <header class="topbar">
                <h1>@yield('title', __('Dashboard'))</h1>
                <div>@yield('actions')</div>
            </header>
            <div class="admin-content">
                <x-flash />
                @yield('content')
            </div>
        </div>
    </div>

    @include('partials.scripts')
</body>
</html>
