<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') · {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="auth">
        <section @class(['auth-side', 'admin' => $isAdmin ?? false])>
            <a href="{{ route('home') }}" class="brand">
                <span class="brand-mark"><x-icon name="book-open" /></span>
                <span>Books<em>Web</em></span>
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
