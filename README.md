
This Laravel package can retrieve and cache vehicle information from the UK DVLA.

## Installation

This package relies upon PHP 8.4, as it uses a number of relatively new language features with which I wanted to get to 
grips.

You can install the package via composer:
```bash
composer require kerrigan/laravel-data
```

The package will automatically register itself. 

Then assign values to the environment variables mentioned in the `dvla.php` configuration file.

You can publish the config file with:
```bash
php artisan vendor:publish --tag="laravel-dvla"
```

These are the contents of the published config file:

```php
// config/dvla.php

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
```

## Usage

### Basic usage

Simply use `app(VehicleLookupService::class)` to instantiate the service according to your configuration. 

Then invoke the `vehicleLookup()` method, passing in a suitable valid UK registration string, e.g. AA01AAA. 