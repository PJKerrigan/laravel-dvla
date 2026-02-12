<?php

declare(strict_types=1);

namespace Kerrigan\Dvla\Exceptions;

use Exception;

class LookupRequest extends Exception
{
    public static function failed(
        int $status,
        string $detail,
        string $registrationNumber,
        string $title,
        ?string $reasonPhrase = null,
    ): self
    {
        // E.g. "404 - Not Found" or "567" when no reason phrase is available.
        $statusString = !empty($reasonPhrase)
            ? sprintf('%s %s', $status, $reasonPhrase)
            : strval($status);

        // Attempts to provide useful information by including the HTTP status code as well as any
        // information we receive from the DVLA API outside of that.
        // E.g. "404 Not Found - AB01CDE - Vehicle Not Found (Record for vehicle not found)"
        // E.g. "403 Forbidden - AB01CDE - Unknown (Unknown error)"
        return new static(
            sprintf("%s - %s - %s (%s)", $statusString, $registrationNumber, $title, $detail),
            $status,
        );
    }
}