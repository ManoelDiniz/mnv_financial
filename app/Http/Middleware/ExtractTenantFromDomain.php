<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ExtractTenantFromDomain
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost(); // Ex: manoel.sejacliente.com.br
        if (preg_match('/^(.*?)\.sejacliente\.com\.br$/', $host, $matches)) {
            $tenant = $matches[1]; // "manoel"
            // Você pode definir uma variável global, session, config, etc.
            // Exemplo: compartilhar com toda a aplicação:
            app()->instance('current_tenant_subdomain', $tenant);
        } else {
            app()->instance('current_tenant_subdomain', null);
        }
        return $next($request);
    }
}
