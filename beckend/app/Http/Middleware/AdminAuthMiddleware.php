<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session('admin_logged_in') && Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        return redirect()->route('admin.login')
            ->with('warning', 'Akses khusus Administrator. Silakan login terlebih dahulu.');
    }
}

