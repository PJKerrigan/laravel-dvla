<?php

declare(strict_types=1);

namespace Kerrigan\Dvla\Tests\DataTransferObjects;

use Kerrigan\Dvla\DataTransferObjects\VehicleLookup;

use function PHPUnit\Framework\assertInstanceOf;

it('correctly instantiates a VehicleLookup from valid data', function () {
    $result = mockVehicleLookup();
    
    assertInstanceOf(VehicleLookup::class, $result);
})->group('DataTransferObjects');