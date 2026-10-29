<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'LibrisCMS'))</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <header class="site-header">
        <div class="header-container">
            <a href="{{ route('home') }}" class="brand-logo">
                <div class="logo-icon">📚</div>
                <div class="logo-text">
                    <span class="logo-title">Libris<strong>CMS</strong></span>
                    <span class="logo-badge">Laravel 11 Edition</span>
                </div>
            </a>
            <nav class="desktop-nav">
                <ul class="nav-links">
                    <li><a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Catalog</a></li>
                    <li><a href="{{ route('admin.dashboard') }}" class="nav-link">Admin CMS</a></li>
                    <li><a href="{{ route('xml.rss') }}" class="nav-link" target="_blank">RSS Feed</a></li>
                </ul>
            </nav>
        </div>
    </header>

    @if(session('success'))
        <div class="flash-container">
            <div class="alert alert-success">{{ session('success') }}</div>
        </div>
    @endif

    <main class="main-content">
        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="footer-container">
            <p>&copy; {{ date('Y') }} LibrisCMS Laravel Edition. Demonstrating MVC, Eloquent & Blade.</p>
        </div>
    </footer>
</body>
</html>

