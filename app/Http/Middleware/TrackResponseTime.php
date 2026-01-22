<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TrackResponseTime
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        
        $response = $next($request);
        
        $responseTime = round((microtime(true) - $startTime) * 1000, 2);
        
        // Log slow requests (> 2 seconds)
        if ($responseTime > 2000) {
            Log::warning('Slow request detected', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'response_time_ms' => $responseTime,
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
            ]);
        }
        
        // Add response time header for debugging
        $response->headers->set('X-Response-Time', $responseTime . 'ms');
        
        return $response;
    }
}
