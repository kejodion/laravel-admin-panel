<?php

namespace Kjjdion\LaravelAdminPanel\Middleware;

class GuestAdmin
{
    public function handle($request, $next, $guard = null)
    {
        if (auth()->guard($guard)->check()) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}