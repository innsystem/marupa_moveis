<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthenticateAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && in_array(Str::lower(Auth::user()->group->name), ['admin', 'developer'])) {
            return $next($request);
        }

        return redirect()->route('login')->withErrors('Acesso não autorizado.');
    }
}
