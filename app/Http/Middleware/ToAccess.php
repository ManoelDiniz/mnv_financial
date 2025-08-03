<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ToAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $allowedOrigins = [];

        if (in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
            return $next($request);
        }

        return response()->json(['msg' => 'Host invalido'], 500);
    }
}
