<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceJsonResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // Without this, Laravel decides JSON-vs-redirect purely from the request's Accept header
    // (Illuminate\Http\Request::expectsJson()) - a client that omits it (e.g. a plain
    // multipart/FormData upload without an explicit Accept header) gets a 302 HTML redirect
    // on validation failure instead of a 422 JSON body. This is an API-only backend, so every
    // response on this route file must be JSON regardless of what the client sent.
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
