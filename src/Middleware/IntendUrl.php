<?php

namespace Kjjdion\LaravelAdminPanel\Middleware;

class IntendUrl
{
    public function handle($request, $next)
    {
        // set intended url for "go back" button
        session()->put('url.intended', $request->url());

        return $next($request);
    }
}