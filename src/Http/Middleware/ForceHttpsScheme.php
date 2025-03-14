<?php

namespace OpenSoutheners\ExtendedLaravel\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\URL;

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
        URL::forceHttps(true);

        return $next($request);
    }
}
