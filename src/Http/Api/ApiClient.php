<?php

declare(strict_types=1);

namespace Kerrigan\Dvla\Http\Api;

use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Kerrigan\Dvla\DataTransferObjects\VehicleLookup;
use Kerrigan\Dvla\Exceptions\LookupRequest;
use Kerrigan\Dvla\Contracts\ApiClient as Contract;

class ApiClient implements Contract
{
    public const string VES_URL_PATH = '/v1/vehicles';
    public const string UNKNOWN_TITLE = 'Unknown';
    public const string UNKNOWN_DETAIL = 'Unknown error';

    public string $apiKey {
        get { return $this->apiKey; }
        set (string $value) {
            $this->apiKey = $value;
        }
    }

    public bool $isLive {
        get { return $this->isLive; }
        set (bool $value) {
            $this->isLive = $value;
        }
    }

    public ?string $correlationId = null {
        get { return $this->correlationId; }
        set (?string $value) {
            $this->correlationId = $value;
        }
    }

    public function __construct(string $apiKey, bool $isLive = false, ?string $correlationId = null) {
        $this->apiKey = $apiKey;
        $this->isLive = $isLive;
        $this->correlationId = $correlationId;
    }
    
    public function baseUrl(): string
    {
        return match ($this->isLive) {
            true  => 'https://driver-vehicle-licensing.api.gov.uk/vehicle-enquiry',
            default => 'https://uat.driver-vehicle-licensing.api.gov.uk/vehicle-enquiry',
        };
    }

    public function vesLookup(string $registrationNumber): VehicleLookup
    {
        $response = Http::baseUrl($this->baseUrl())
            ->withHeaders(array_filter([
                "x-api-key" => $this->apiKey,
                'Content-Type' => "application/json",
                'X-Correlation-Id' => $this->correlationId,
            ]))
            ->post(static::VES_URL_PATH, [
                'registrationNumber' => $registrationNumber,
            ]);

        if ($response->getStatusCode() !== 200) {
            $errors = json_decode($response->getBody()->getContents(), true);
            $error = $errors['errors'][0] ?? [];

            throw LookupRequest::failed(
                status: $response->getStatusCode(),
                detail: $error['detail'] ?? static::UNKNOWN_DETAIL,
                registrationNumber: $registrationNumber,
                title: $error['title'] ?? static::UNKNOWN_TITLE,
                reasonPhrase: $response->getReasonPhrase(),
            );
        }

        $decoded = json_decode($response->getBody()->getContents(), true);

        return VehicleLookup::from($decoded);
    }
}