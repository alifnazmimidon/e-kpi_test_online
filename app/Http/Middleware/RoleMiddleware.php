<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // 1. Jika belum login, tendang semula ke halaman login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 2. Jika sudah login tetapi cuba masuk halaman role lain, paparkan 403
        $userRole = strtolower($request->user()->role ?? '');

        if (!in_array($userRole, array_map('strtolower', $roles))) {
            abort(403, 'Akses ditolak. Anda tidak mempunyai kebenaran untuk mengakses halaman ini.');
        }

        $response = $next($request);

        // 3. Halang browser simpan cache halaman selepas logout (elak butang Back)
        $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');

        return $response;
    }
}