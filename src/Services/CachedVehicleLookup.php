<?php

declare(strict_types=1);

namespace Kerrigan\Dvla\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Kerrigan\Dvla\DataTransferObjects\VehicleLookup as DataTransferObject;
use Kerrigan\Dvla\Contracts\ApiClient;
use Kerrigan\Dvla\Contracts\VehicleLookupService as Contract;
use Throwable;

readonly class CachedVehicleLookup implements Contract
{
    public const string DEFAULT_CACHE_PREFIX = 'dvla_vehicle_lookup_';
    
    public function __construct(
        public ApiClient $client,
        public string $cachePrefix = self::DEFAULT_CACHE_PREFIX,
        public bool $reportErrors = true,
    ) {
    }
    
    public function getCacheKey(string $registrationNumber): string
    {
        return $this->cachePrefix . $registrationNumber;
    }
    
    public function vehicleLookup(string $registrationNumber): ?DataTransferObject
    {
        return Cache::remember(
            $this->getCacheKey($registrationNumber),
            CarbonImmutable::today()->endOfDay(),
            function () use ($registrationNumber) {
                try {
                    return $this->client->vesLookup($registrationNumber);
                } catch (Throwable $th) {
                    if ($this->reportErrors) {
                        report($th);
                    }
                    
                    return null;
                }
            },
        );
    }
}
