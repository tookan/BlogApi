<?php

namespace App\Http\Middleware;

use Closure;

class cors
{

        public function handle($request, Closure $next)
    {
        return $next($request)
            ->header('Access-Control-Allow-Origin', 'http://localhost:4200')
            ->header('Access-Control-Allow-Methods', '*');
    }

}
