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
            if ($globalSettings->is_rituals_premium_only) {
                return $this->deny();
            }

            // Check individual ritual if it's a detail request
            $ritual = $request->route()->parameter('ritual');
            if ($ritual instanceof \App\Models\Ritual && $ritual->is_premium) {
                return $this->deny();
            }
        }

        // Incantations
        if (str_contains($path, 'incantations')) {
            if ($globalSettings->is_incantations_premium_only) {
                return $this->deny();
            }

            // Check individual incantation if it's a detail request
            $incantation = $request->route()->parameter('incantation');
            if ($incantation instanceof \App\Models\Incantation && $incantation->is_paid) {
                return $this->deny();
            }
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
