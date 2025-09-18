<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MobileOnlyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Debug logging
        \Log::info('MobileOnlyMiddleware - URL: ' . $request->url());
        \Log::info('MobileOnlyMiddleware - AJAX: ' . ($request->ajax() ? 'true' : 'false'));
        \Log::info('MobileOnlyMiddleware - WantsJson: ' . ($request->wantsJson() ? 'true' : 'false'));
        \Log::info('MobileOnlyMiddleware - X-Requested-With: ' . $request->header('X-Requested-With'));
        
        // Allow AJAX requests to pass through without mobile check
        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            \Log::info('MobileOnlyMiddleware - Allowing AJAX request');
            return $next($request);
        }

        // Check if user has forced mobile view via cookie or query parameter
        $forceMobile = $request->cookie('force_mobile_view') ?? $request->query('force_mobile');
        if ($forceMobile === 'true' || $forceMobile === '1' || $forceMobile === true) {
            return $next($request);
        }

        // Check if it's a mobile device
        $userAgent = $request->header('User-Agent');
        $isMobile = $this->isMobileDevice($userAgent);

        // If not mobile and not authenticated, show mobile-only page
        if (!$isMobile && !auth()->check()) {
            return response()->view('mobile-only');
        }

        // If not mobile but authenticated, allow access but add mobile warning
        if (!$isMobile && auth()->check()) {
            // Add mobile warning to session
            session()->flash('mobile_warning', true);
        }

        return $next($request);
    }

    /**
     * Check if the user agent indicates a mobile device
     */
    private function isMobileDevice($userAgent): bool
    {
        if (!$userAgent) {
            return false;
        }

        $mobileKeywords = [
            'Mobile', 'Android', 'iPhone', 'iPad', 'iPod',
            'BlackBerry', 'Windows Phone', 'Opera Mini',
            'IEMobile', 'Mobile Safari'
        ];

        foreach ($mobileKeywords as $keyword) {
            if (stripos($userAgent, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }
}
