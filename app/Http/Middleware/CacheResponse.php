<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;

class CacheResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, int $ttl = 300): Response
    {
        // Only cache GET requests
        if (!$request->isMethod('GET')) {
            return $next($request);
        }

        // Don't cache authenticated user-specific pages
        if ($request->user()) {
            return $next($request);
        }

        // Generate cache key from the request
        $cacheKey = 'route_cache_' . md5($request->url() . $request->getQueryString());

        // Check if response is cached
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // Get response
        $response = $next($request);

        // Cache successful responses
        if ($response->isSuccessful()) {
            Cache::put($cacheKey, $response, $ttl);
        }

        return $response;
    }
}
