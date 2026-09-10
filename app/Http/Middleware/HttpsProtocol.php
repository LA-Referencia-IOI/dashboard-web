<?php

namespace App\Http\Middleware;

use Closure;

class HttpsProtocol
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // TLS terminates at the deployment's external reverse proxy. The
        // application container is intentionally HTTP-only in every profile;
        // redirecting here breaks local access and proxy deployments that do
        // not forward the original HTTPS scheme.
        return $next($request);
    }
}
