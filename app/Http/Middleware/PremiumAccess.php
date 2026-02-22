<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PremiumAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && !$request->user()->hasActivePremium()) {
            return response()->json([
                'message' => 'Unlock this feature with Watered Plus+',
                'error' => 'PREMIUM_REQUIRED'
            ], 403);
        }

        return $next($request);
    }
}
