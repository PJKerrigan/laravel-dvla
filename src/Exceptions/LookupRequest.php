<?php

declare(strict_types=1);

namespace Kerrigan\Dvla\Exceptions;

use Exception;

class LookupRequest extends Exception
{
    public static function failed(
        string $code,
        string $detail,
        string $registrationNumber,
        int $status,
        string $title,
    ): self
    {
        return new static(
            sprintf("%s (%s) %s: %s", $registrationNumber, $code, $title, $detail),
            $status,
        );
    }
}