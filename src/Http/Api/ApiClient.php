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
    public const string VALID_REGEXP = "/(^[A-Z]{2}[0-9]{2}\s?[A-Z]{3}$)|(^[A-Z][0-9]{1,3}[A-Z]{3}$)|(^[A-Z]{3}[0-9]{1,3}[A-Z]$)|(^[0-9]{1,4}[A-Z]{1,2}$)|(^[0-9]{1,3}[A-Z]{1,3}$)|(^[A-Z]{1,2}[0-9]{1,4}$)|(^[A-Z]{1,3}[0-9]{1,3}$)|(^[A-Z]{1,3}[0-9]{1,4}$)|(^[0-9]{3}[DX]{1}[0-9]{3}$)/i";
    public const string VES_URL_PATH = '/v1/vehicles';
    
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
    
    public string $baseUrl {
        get {
            return match ($this->isLive) {
                true  => 'https://driver-vehicle-licensing.api.gov.uk/vehicle-enquiry',
                false => 'https://uat.driver-vehicle-licensing.api.gov.uk/vehicle-enquiry',
            };
        }
    }

    public function __construct(string $apiKey, bool $isLive = false, ?string $correlationId = null) {
        $this->apiKey = $apiKey;
        $this->isLive = $isLive;
        $this->correlationId = $correlationId;
    }
    
    public function vesLookup(string $registrationNumber): VehicleLookup
    {
        preg_match(self::VALID_REGEXP, $registrationNumber, $matches);
        
        if (empty($matches)) {
            throw new InvalidArgumentException(sprintf(
                'Registration number `%s` is invalid.',
                $registrationNumber,
            ));
        }

        $response = Http::baseUrl($this->baseUrl)
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
            $error = $errors['errors'][0];

            throw LookupRequest::failed(
                code: $error['code'] ?? 'unknown',
                detail: $error['detail'] ?? 'unknown',
                registrationNumber: $registrationNumber,
                status: $response->getStatusCode(),
                title: $error['title'] ?? 'unknown',
            );
        }

        $decoded = json_decode($response->getBody()->getContents(), true);

        return VehicleLookup::from($decoded);
    }
}