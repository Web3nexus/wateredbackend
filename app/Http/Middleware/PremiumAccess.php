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
        $user = $request->user();

        // If user is premium, they can access everything
        if ($user && $user->hasActivePremium()) {
            return $next($request);
        }

        // For free users, check global overrides and individual item status
        $globalSettings = \App\Models\GlobalSetting::first();
        if (!$globalSettings) {
            return $next($request);
        }

        $path = $request->path();

        // Rituals
        if (str_contains($path, 'rituals')) {
            $ritual = $request->route()->parameter('ritual');

            // If it's a detail request (ritual parameter exists)
            if ($ritual instanceof \App\Models\Ritual) {
                return $ritual->is_premium ? $this->deny() : $next($request);
            }

            // For the index listing, ALWAYS allow it so users can see the content.
            // Individual record access is blocked above if premium.
            return $next($request);
        }

        // Incantations
        if (str_contains($path, 'incantations')) {
            $incantation = $request->route()->parameter('incantation');

            if ($incantation instanceof \App\Models\Incantation) {
                return ($incantation->is_premium || $incantation->is_paid) ? $this->deny() : $next($request);
            }

            return $next($request);
        }

        return $next($request);
    }

    protected function deny()
    {
        return response()->json([
            'message' => 'Unlock this feature with Watered Plus+',
            'error' => 'PREMIUM_REQUIRED'
        ], 403);
    }
}
