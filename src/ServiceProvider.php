<?php

declare(strict_types=1);

namespace Kerrigan\Dvla;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Kerrigan\Dvla\Contracts\ApiClient;
use Kerrigan\Dvla\Http\Api\ApiClient as ConcreteApiClient;
use Kerrigan\Dvla\Contracts\VehicleLookupService;
use Kerrigan\Dvla\Services\CachedVehicleLookup;
use Kerrigan\Dvla\Services\VehicleLookup;
use Illuminate\Contracts\Foundation\Application;

class ServiceProvider extends IlluminateServiceProvider
{
    public function register(): void
    {
        $this->publishes([
            __DIR__.'/../config/dvla.php' => config_path('dvla.php'),
        ], 'laravel-dvla');

        $this->mergeConfigFrom(
            __DIR__.'/../config/dvla.php', 'dvla'
        );
        
        // Create ApiClient.
        $this->app->singleton(
            ApiClient::class, 
            fn (Application $app) => new ConcreteApiClient(
                apiKey: $this->app['config']->get('dvla.api.key', ''),
                isLive: $this->app['config']->get('dvla.api.is_live', false),
                correlationId: $this->app['config']->get('dvla.api.correlation_id', null),
            ),
        );
        
        // Create VehicleLookupService.
        if (config('dvla.cache_enabled', false)) {
            $this->app->singleton(
                VehicleLookupService::class, 
                fn (Application $app) => new CachedVehicleLookup(
                    $app->make(ApiClient::class),
                    $app['config']->get('dvla.cache_prefix', CachedVehicleLookup::DEFAULT_CACHE_PREFIX),
                ),
            );
        } else {
            $this->app->singleton(
                VehicleLookupService::class,
                fn (Application $app) => new VehicleLookup($app->make(ApiClient::class)));
        }
    }
}