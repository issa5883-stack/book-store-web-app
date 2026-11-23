<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') · {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400..700&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="auth">
        <section @class(['auth-side', 'admin' => $isAdmin ?? false])>
            <a href="{{ route('home') }}" class="brand">
                <span class="brand-mark" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor"><circle cx="6.5" cy="6.5" r="4.5"/><circle cx="17.5" cy="6.5" r="4.5"/><circle cx="6.5" cy="17.5" r="4.5"/><rect x="13" y="13" width="9" height="9" rx="1.5"/></svg></span>
                <span>BooksWeb</span>
            </a>
            <div>
                @yield('side')
            </div>
            <p class="small" style="margin: 0;">© {{ date('Y') }} {{ config('app.name') }}</p>
        </section>

        <main class="auth-main">
            <div class="auth-box">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
