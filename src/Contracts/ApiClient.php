<?php

declare(strict_types=1);

namespace Kerrigan\Dvla\Contracts;

use Kerrigan\Dvla\DataTransferObjects\VehicleLookup;

interface ApiClient
{
    public function vesLookup(string $registrationNumber): VehicleLookup;
}