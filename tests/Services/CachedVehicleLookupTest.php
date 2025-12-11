<?php

declare(strict_types=1);

namespace Kerrigan\Dvla\Tests\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Kerrigan\Dvla\Contracts\ApiClient;
use Kerrigan\Dvla\Enums\MotStatus;
use Kerrigan\Dvla\Enums\TaxStatus;
use Kerrigan\Dvla\Services\CachedVehicleLookup;
use Kerrigan\Dvla\DataTransferObjects\VehicleLookup as DataTransferObject;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertNull;

beforeEach(function () {
    /**
     * @var ApiClient
     */
    $this->apiClient = App::make(ApiClient::class);
    $this->service = new CachedVehicleLookup(
        client: $this->apiClient,
        cachePrefix: 'dvla_vehicle_lookup_',
        reportErrors: true,
    );
    
    $this->stub404 = json_decode(stubFile('404.json'), true);
    $this->stubValid = json_decode(stubFIle('valid.json'), true);
    
    $this->testUrl = 'https://uat.driver-vehicle-licensing.api.gov.uk/vehicle-enquiry/v1/vehicles';
    $this->liveUrl = 'https://uat.driver-vehicle-licensing.api.gov.uk/vehicle-enquiry/v1/vehicles';
});

it('returns a VehicleLookup for a valid registrationNumber that exists', function () {
    Http::fake([$this->testUrl => Http::response($this->stubValid)]);

    $result = $this->service->vehicleLookup('AA01AAA');

    assertInstanceOf(DataTransferObject::class, $result);
    assertEquals('AA01AAA', $result->registrationNumber);
    assertEquals(TaxStatus::Taxed, $result->taxStatus);
    assertEquals(MotStatus::Valid, $result->motStatus);
    assertEquals('MAZDA', $result->make);
    assertEquals('GREEN', $result->colour);
    assertEquals('PETROL', $result->fuelType);
    assertEquals(false, $result->markedForExport);
    assertEquals(2009, $result->yearOfManufacture);
    assertEquals(2199, $result->engineCapacity);
    assertEquals(194, $result->co2Emissions);
    assertEquals(Carbon::parse('2026-09-01','Europe/London')->startOfDay(), $result->taxDueDate,);
    assertEquals(Carbon::parse('2022-09-04', 'Europe/London')->startOfDay(), $result->dateOfLastV5CIssued);
    assertEquals(Carbon::parse('2026-03-25', 'Europe/London')->startOfDay(), $result->motExpiryDate);
    assertEquals('M1', $result->typeApproval);
    assertEquals('2 AXLE RIGID BODY', $result->wheelplan);
    assertEquals('2009-07', $result->monthOfFirstRegistration);
})->group('Services', 'VehicleLookup');

it('returns null for a valid registrationNumber that does not exist ', function () {
    Http::fake([$this->testUrl => Http::response($this->stub404)]);

    $result = $this->service->vehicleLookup('AA01AAA');

    assertNull($result);
})->group('Services', 'VehicleLookup');

it('retrieves cached results for a valid registrationNumber that does exist', function () {
    $registrationNumber = 'AA01AAA';
    Cache::put($this->service->getCacheKey($registrationNumber), DataTransferObject::from($this->stubValid), Carbon::today()->endOfDay());

    // Trigger the cache read.
    $result = $this->service->vehicleLookup($registrationNumber);

    assertInstanceOf(DataTransferObject::class, $result);
    assertEquals('AA01AAA', $result->registrationNumber);
    assertEquals(TaxStatus::Taxed, $result->taxStatus);
    assertEquals(MotStatus::Valid, $result->motStatus);
    assertEquals('MAZDA', $result->make);
    assertEquals('GREEN', $result->colour);
    assertEquals('PETROL', $result->fuelType);
    assertEquals(false, $result->markedForExport);
    assertEquals(2009, $result->yearOfManufacture);
    assertEquals(2199, $result->engineCapacity);
    assertEquals(194, $result->co2Emissions);
    assertEquals(Carbon::parse('2026-09-01','Europe/London')->startOfDay(), $result->taxDueDate,);
    assertEquals(Carbon::parse('2022-09-04', 'Europe/London')->startOfDay(), $result->dateOfLastV5CIssued);
    assertEquals(Carbon::parse('2026-03-25', 'Europe/London')->startOfDay(), $result->motExpiryDate);
    assertEquals('M1', $result->typeApproval);
    assertEquals('2 AXLE RIGID BODY', $result->wheelplan);
    assertEquals('2009-07', $result->monthOfFirstRegistration);
})->group('Services', 'VehicleLookup');

it('retrieves cached results for a valid registrationNumber that does not exist', function () {
    // Trigger the cache write; Cache::put() did not work for `null` so I am invoking the logic directly.
    $registrationNumber = 'AA01AAA';
    Http::fake([$this->testUrl => Http::response($this->stub404)]);
    $this->service->vehicleLookup($registrationNumber);
    
    // Attempt a second lookup of the same registration number, hoping to read from cache.
    Http::fake([$this->testUrl => Http::response($this->stubValid)]);
    
    $result = $this->service->vehicleLookup($registrationNumber);
    assertNull($result);
})->group('Services', 'VehicleLookup');
