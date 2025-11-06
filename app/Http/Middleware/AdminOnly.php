<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->session()->get('portal_user');
        if (!$user || ($user['role'] ?? null) !== 'admin') {
            abort(403, 'Admins only.');
        }
        return $next($request);
    }
}

