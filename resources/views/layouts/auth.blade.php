<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ config('app.supported_locales.'.app()->getLocale().'.dir', 'ltr') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#fafaf9">
    <title>@yield('title') · {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400..700{{ app()->getLocale() === 'ar' ? '&family=Cairo:wght@400..700' : '' }}&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="auth-page">
    <div class="auth-lang"><x-language-switcher class="btn btn-ghost btn-sm" /></div>

    <main class="auth">
        <div class="auth-card">
            <a href="{{ route('home') }}" class="brand auth-brand">
                <span class="brand-mark" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor"><circle cx="6.5" cy="6.5" r="4.5"/><circle cx="17.5" cy="6.5" r="4.5"/><circle cx="6.5" cy="17.5" r="4.5"/><rect x="13" y="13" width="9" height="9" rx="1.5"/></svg></span>
                <span>BooksWeb</span>
            </a>

            @yield('content')
        </div>

        <p class="auth-copy">© {{ date('Y') }} {{ config('app.name') }}</p>
    </main>

    @include('partials.auth-scripts')
</body>
</html>
