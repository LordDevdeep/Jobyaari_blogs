<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') · JobYaari</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}" rel="stylesheet">
    @stack('head')
</head>
<body>
    <div class="admin-shell">
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="admin-brand">
                <span class="jy-brand-mark">JY</span>
                <span>JobYaari Admin</span>
            </div>
            <nav class="admin-nav">
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge-high"></i> Dashboard
                </a>
                <a href="{{ route('admin.blogs.index') }}" class="{{ request()->routeIs('admin.blogs.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-newspaper"></i> Blogs
                </a>
                <a href="{{ route('home') }}" target="_blank">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> View Site
                </a>
                <div class="admin-logout">
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
                    </form>
                </div>
            </nav>
        </aside>

        <div class="admin-main">
            <div class="admin-topbar">
                <button type="button" class="admin-toggle" id="adminToggle" aria-label="Open menu"><i class="fa-solid fa-bars"></i></button>
                <div style="flex:1"></div>
                <div class="admin-user-chip">
                    <span class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</span>
                    <span>{{ auth()->user()->name ?? 'Admin' }}</span>
                </div>
            </div>
            <main class="admin-content">
                @if (session('success'))
                    <div class="flash success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="flash error"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        document.getElementById('adminToggle')?.addEventListener('click', () => {
            document.getElementById('adminSidebar').classList.toggle('open');
        });
        document.addEventListener('click', (e) => {
            const sb = document.getElementById('adminSidebar');
            const tb = document.getElementById('adminToggle');
            if (window.innerWidth <= 768 && sb && !sb.contains(e.target) && !tb.contains(e.target)) {
                sb.classList.remove('open');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
