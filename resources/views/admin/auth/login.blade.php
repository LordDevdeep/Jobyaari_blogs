<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login · JobYaari</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}" rel="stylesheet">
</head>
<body>
    <div class="login-shell">
        <div class="login-card">
            <div class="brand-mark" style="display:flex;align-items:center;gap:10px;">
                <span class="jy-brand-mark" style="width:40px;height:40px;font-size:16px;">JY</span>
                <span style="font-size:18px;font-weight:600;">JobYaari Admin</span>
            </div>
            <h1>Welcome back</h1>
            <p>Sign in to manage blog content.</p>

            @if (session('error'))
                <div class="flash error">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}" novalidate>
                @csrf
                <div class="form-group">
                    <label for="email">Email address</label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
                    @error('email') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                    @error('password') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:8px;font-weight:400;cursor:pointer;">
                        <input type="checkbox" name="remember" value="1"> Remember me
                    </label>
                </div>
                <button type="submit" class="jy-btn" style="width:100%;justify-content:center;">
                    <i class="fa-solid fa-right-to-bracket"></i> Sign In
                </button>
            </form>

            <div style="margin-top:20px;padding-top:20px;border-top:1px solid var(--color-border);font-size:13px;color:var(--color-text-muted);text-align:center;">
                <a href="{{ route('home') }}"><i class="fa-solid fa-arrow-left"></i> Back to site</a>
            </div>
        </div>
    </div>
</body>
</html>
