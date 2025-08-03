<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IuguAuthenticate
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
        // se o token for igual ao token auth iugu
        if ($request->bearerToken() === config('services.iugu.auth')) {
            return $next($request);
        }

        // erro, não autorizado
        return abort(401);
    }
}
