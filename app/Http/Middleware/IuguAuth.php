<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IuguAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // se o token for igual ao token auth iugu
        if ($request->bearerToken() === config('services.iugu.auth')) {
            return $next($request);
        }

        // erro, não autorizado
        return abort(401);
    }
}
