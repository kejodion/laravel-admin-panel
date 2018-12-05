<?php

namespace Kjjdion\LaravelAdminPanel\Middleware;

use Illuminate\Auth\Access\AuthorizationException;

class NotSystemDoc
{
    public function handle($request, $next)
    {
        // check if doc is system
        if (app(config('lap.models.doc'))->where('id', $request->route()->parameter('id'))->where('system', true)->first()) {
            throw new AuthorizationException();
        }

        return $next($request);
    }
}