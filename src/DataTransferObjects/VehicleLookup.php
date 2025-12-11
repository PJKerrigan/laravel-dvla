<?php

namespace Kerrigan\Dvla\DataTransferObjects;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Kerrigan\Dvla\Enums\MotStatus;
use Kerrigan\Dvla\Enums\TaxStatus;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;

class VehicleLookup extends Data
{
    public function __construct(
        public string $registrationNumber,
        #[WithCast(DateTimeInterfaceCast::class, type: CarbonImmutable::class, format: '!Y-m-d', timeZone: 'Europe/London')]
        public ?CarbonInterface $artEndDate = null,
        public ?bool $automatedVehicle = null,
        public ?int $co2Emissions = null,
        public ?string $colour = null,
        #[WithCast(DateTimeInterfaceCast::class, type: CarbonImmutable::class, format: '!Y-m-d', timeZone: 'Europe/London')]
        public ?CarbonInterface $dateOfLastV5CIssued = null,
        public ?int $engineCapacity = null,
        public ?string $euroStatus = null,
        public ?string $fuelType = null,
        public ?string $make = null,
        public ?bool $markedForExport = null,
        public ?string $monthOfFirstDvlaRegistration = null,
        public ?string $monthOfFirstRegistration = null,
        #[WithCast(DateTimeInterfaceCast::class, type: CarbonImmutable::class, format: '!Y-m-d', timeZone: 'Europe/London')]
        public ?CarbonInterface $motExpiryDate = null,
        #[WithCast(EnumCast::class, type: MotStatus::class)]
        public ?MotStatus $motStatus = null,
        public ?string $realDrivingEmissions = null,
        public ?int $revenueWeight = null,
        #[WithCast(DateTimeInterfaceCast::class, type: CarbonImmutable::class, format: '!Y-m-d', timeZone: 'Europe/London')]
        public ?CarbonInterface $taxDueDate = null,
        #[WithCast(EnumCast::class, type: TaxStatus::class)]
        public ?TaxStatus $taxStatus = null,
        public ?string $typeApproval = null,
        public ?string $wheelplan = null,
        public ?int $yearOfManufacture = null,
    ) {
    }
}