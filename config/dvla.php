<?php

declare(strict_types=1);

use Kerrigan\Dvla\Services\CachedVehicleLookup;

return [
    /*
     * DVLA VES API configuration.
     */
    'api' => [
        /*
         * API key. 
         */
        'key' => env('DVLA_API_KEY', ''),
        
        /*
         * Use the live API?
         */
        'is_live' => env('DVLA_API_IS_LIVE', false),
        
        /*
         * Use a correlation ID for debugging purposes?
         */
        'correlation_id' => env('DVLA_API_CORRELATION_ID', null),
    ],

    /*
     * Should results be cached? The cache will keep results until midnight. DVLA data is updated 
     * daily, meaning that results queried via the VES lookup API will be valid until at least midnight. 
     */
    'cache_enabled' => env('DVLA_CACHE_ENABLED', false),
    
    /*
     * If `cache_enabled` is set to true, what prefix should be used to cache results?
     */
    'cache_prefix' => env('DVLA_CACHE_PREFIX', CachedVehicleLookup::DEFAULT_CACHE_PREFIX),
];