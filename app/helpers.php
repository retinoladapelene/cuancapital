<?php

if (!function_exists('isMobile')) {
    /**
     * Detect if the current request is from a mobile device.
     *
     * Usage in Blade:
     *   @if(isMobile())
     *       {{-- mobile-specific content --}}
     *   @else
     *       {{-- desktop content --}}
     *   @endif
     *
     * @return bool
     */
    function isMobile(): bool
    {
        $agent = request()->header('User-Agent', '');

        return (bool) preg_match(
            '/Mobile|Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini|webOS/i',
            $agent
        );
    }
}

if (!function_exists('isTablet')) {
    /**
     * Detect tablet-range devices.
     * @return bool
     */
    function isTablet(): bool
    {
        $agent = request()->header('User-Agent', '');
        return (bool) preg_match('/iPad|Tablet|Kindle|PlayBook|Nexus (7|9|10)/i', $agent);
    }
}

if (!function_exists('isMobileOrTablet')) {
    /**
     * Detect any touch-primary device.
     * @return bool
     */
    function isMobileOrTablet(): bool
    {
        return isMobile() || isTablet();
    }
}
