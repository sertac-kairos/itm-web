<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminExists
{
    public function handle(Request $request, Closure $next): Response
    {
        if (User::count() === 0 && !$request->is('login') && !$request->is('register')) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}


