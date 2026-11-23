<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Digital Library') · {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header class="navbar">
        <div class="container">
            <a href="{{ route('home') }}" class="brand">
                <span class="brand-mark"><x-icon name="book-open" /></span>
                <span>Books<em>Web</em></span>
            </a>

            <nav class="nav-links">
                <a href="{{ route('home') }}" @class(['active' => request()->routeIs('home')])>Home</a>
                <a href="{{ route('books.index') }}" @class(['active' => request()->routeIs('books.*')])>Browse Books</a>
                @auth
                    <a href="{{ route('my-books.index') }}" @class(['active' => request()->routeIs('my-books.index', 'my-books.edit')])>My Books</a>
                    <a href="{{ route('my-books.create') }}" @class(['active' => request()->routeIs('my-books.create')])>Submit a Book</a>
                @endauth
            </nav>

            <div class="nav-actions">
                @auth
                    <a href="{{ route('my-books.create') }}" class="btn btn-primary btn-sm"><x-icon name="upload" /> Upload</a>
                    <div class="user-chip">
                        <span class="avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                        <span class="name">{{ auth()->user()->name }}</span>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-ghost btn-icon" title="Sign out"><x-icon name="logout" /></button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">Sign in</a>
                    <a href="{{ route('sign_up') }}" class="btn btn-primary btn-sm">Create account</a>
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
                    <span class="brand-mark"><x-icon name="book-open" /></span>
                    <span>Books<em>Web</em></span>
                </a>
                <p style="margin-top: 1rem; max-width: 22rem;">A community digital library. Read, download and share books — every upload is reviewed by our team.</p>
            </div>
            <div class="footer-links">
                <a href="{{ route('books.index') }}">Browse</a>
                @auth
                    <a href="{{ route('my-books.create') }}">Submit a book</a>
                @else
                    <a href="{{ route('sign_up') }}">Join</a>
                @endauth
                <a href="{{ route('admin.login') }}">Admin</a>
            </div>
        </div>
        <div class="container footer-bottom">© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</div>
    </footer>

    @include('partials.scripts')
</body>
</html>
