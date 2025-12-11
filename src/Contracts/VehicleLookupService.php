<?php

declare(strict_types=1);

namespace Kerrigan\Dvla\Contracts;

use Kerrigan\Dvla\DataTransferObjects\VehicleLookup;

interface VehicleLookupService
{
    public function vehicleLookup(string $registrationNumber): ?VehicleLookup;
}