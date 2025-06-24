<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsEmployee
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->isEmployee()) {
            abort(403, 'Acceso no autorizado.');
        }

        return $next($request);
    }
}
