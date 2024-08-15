<?php

namespace OpenSoutheners\ExtendedLaravel\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class ForceHttpsScheme
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Str::contains($request->referer ?: $request->url(), 'https')) {
            URL::forceScheme('https');
        }

        return $next($request);
    }
}
