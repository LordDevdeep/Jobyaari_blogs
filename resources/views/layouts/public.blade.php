<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'JobYaari Blogs — Latest Government Jobs, Admit Cards, Results')</title>
    <meta name="description" content="@yield('description', 'JobYaari blog: latest government job notifications, admit cards, results, answer keys and syllabi — curated for Indian aspirants.')">
    @stack('og')

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}" rel="stylesheet">
</head>
<body>
    <nav class="jy-navbar">
        <div class="container">
            <a href="{{ route('home') }}" class="jy-brand">
                <span class="jy-brand-mark">JY</span>
                <span>JobYaari Blogs</span>
            </a>
            <div class="jy-nav-actions">
                <a href="{{ route('admin.login.show') }}" class="jy-btn secondary jy-btn-sm">
                    <i class="fa-solid fa-lock"></i> Admin
                </a>
            </div>
        </div>
    </nav>

    @yield('content')

    <footer class="jy-footer">
        <div>&copy; {{ date('Y') }} JobYaari Blogs. Built for the JobYaari developer internship.</div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    </script>
    @stack('scripts')
</body>
</html>
