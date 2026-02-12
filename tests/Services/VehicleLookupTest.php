<?php

declare(strict_types=1);

namespace Kerrigan\Dvla\Tests\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Kerrigan\Dvla\Contracts\ApiClient;
use Kerrigan\Dvla\Enums\MotStatus;
use Kerrigan\Dvla\Enums\TaxStatus;
use Kerrigan\Dvla\Services\VehicleLookup;
use Kerrigan\Dvla\DataTransferObjects\VehicleLookup as DataTransferObject;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertNull;

beforeEach(function () {
    /**
     * @var ApiClient
     */
    $this->apiClient = App::make(ApiClient::class);
    $this->service = new VehicleLookup(
        client: $this->apiClient,
        reportErrors: true,
    );
});

it('returns a VehicleLookup for a valid registrationNumber that exists', function () {
    $decodedStub = json_decode(stubFile('valid.json'), true);
    Http::fake([
        'https://uat.driver-vehicle-licensing.api.gov.uk/vehicle-enquiry/v1/vehicles' => 
            Http::response($decodedStub, 200, []),
    ]);
    
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
$decodedStub = json_decode(stubFile('404.json'), true);
    Http::fake([
        'https://uat.driver-vehicle-licensing.api.gov.uk/vehicle-enquiry/v1/vehicles' =>
            Http::response($decodedStub, 200, []),
    ]);

    $result = $this->service->vehicleLookup('AA01AAA');
    
    assertNull($result);
})->group('Services', 'VehicleLookup');
