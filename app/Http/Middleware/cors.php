<?php

namespace App\Http\Middleware;

use Closure;

class cors
{

        public function handle($request, Closure $next)
    {
        return $next($request)
            ->header('Access-Control-Allow-Origin', 'http://learningblog.itcraftlab.com')
            ->header('Access-Control-Allow-Methods', '*');
    }

}
