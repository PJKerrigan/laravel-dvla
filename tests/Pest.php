<?php

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Kerrigan\Dvla\DataTransferObjects\VehicleLookup;
use Kerrigan\Dvla\Enums\MotStatus;
use Kerrigan\Dvla\Enums\TaxStatus;
use Kerrigan\Dvla\Tests\TestCase;

uses(TestCase::class)
    ->beforeEach(function ()
    {
        setTestNow(CarbonImmutable::now()->startOfSecond());
    })
    ->in(__DIR__);

function mockVehicleLookup(array $attributes = []): VehicleLookup
{
    return createVehicleLookup(
        array_merge(
            [
                'registrationNumber' => 'AA01AAA',
                'artEndDate' => Carbon::yesterday()->format('Y-m-d'),
                'automatedVehicle' => false,
                'co2Emissions' => 123,
                'colour' => 'BLUE',
                'dateOfLastV5CIssued' => Carbon::yesterday()->subMonths(1)->format('Y-m-d'),
                'engineCapacity' => 998,
                'euroStatus' => null,
                'fuelType' => 'PETROL',
                'make' => 'DAIHATSU',
                'markedForExport' => false,
                'monthOfFirstDvlaRegistration' => '2020-01',
                'monthOfFirstRegistration' => '2020-01',
                'motExpiryDate' => Carbon::tomorrow()->addMOnths(1)->format('Y-m-d'),
                'motStatus' => MotStatus::Valid->value,
                'realDrivingEmissions' => null,
                'revenueWeight' => 1120,
                'taxDueDate' => Carbon::tomorrow()->addMonths(1)->format('Y-m-d'),
                'taxStatus' => TaxStatus::Taxed->value,
                'typeApproval' => 'M1',
                'yearOfManufacture' => '2014',
            ],
            $attributes,
        )
    );
}

function createVehicleLookup(array $attributes): VehicleLookup
{
    return VehicleLookup::from($attributes);
}

function setTestNow(?CarbonInterface $value): void
{
    Carbon::setTestNow($value);
    CarbonImmutable::setTestNow($value);
}

function stubFile(string $path): string
{
    return file_get_contents(
        realpath(__DIR__ . '/stubs/' . ltrim($path, '/')),
    );
}