<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect()->route('admin.login.show')
                ->with('error', 'Please log in to access the admin area.');
        }

        if (! auth()->user()->is_admin) {
            auth()->logout();
            return redirect()->route('admin.login.show')
                ->with('error', 'You do not have permission to access the admin area.');
        }

        return $next($request);
    }
}
