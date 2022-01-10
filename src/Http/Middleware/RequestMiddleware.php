<?php

namespace Yannice92\LumenInterceptor\Http\Middleware;

use Closure;
use Faker\Provider\Uuid;
use Illuminate\Http\Request;

class RequestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->hasHeader("x-request-id")) {
            $request->headers->set("x-request-id", Uuid::uuid(),false);
        }
        return $next($request);
    }
}
