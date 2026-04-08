<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CcheckRole
{
    public function handle(Request $request, Closure $next, $role)
{

if (!Auth::check() || Auth::user()->role !== $role) {
abort(403, 'Akses Ditolak');
}
    return $next($request);
    }
}