<?php

declare(strict_types=1);

namespace Kerrigan\Dvla\Services;

use Kerrigan\Dvla\DataTransferObjects\VehicleLookup as DataTransferObject;
use Kerrigan\Dvla\Contracts\ApiClient;
use Kerrigan\Dvla\Contracts\VehicleLookupService as Contract;
use Throwable;

readonly class VehicleLookup implements Contract
{
    public function __construct(
        public ApiClient $client,
        public bool $reportErrors = true,
    ) {
    }

    public function vehicleLookup(string $registrationNumber): ?DataTransferObject
    {
        try {
            return $this->client->vesLookup($registrationNumber);
        } catch (Throwable $th) {
            if ($this->reportErrors) {
                report($th);
            }

            return null;
        }
    }
}
