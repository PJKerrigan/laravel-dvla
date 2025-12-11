<?php

declare(strict_types=1);

namespace Kerrigan\Dvla\Enums;

enum MotStatus: string
{
    case NoDetailsHeldByDvla = 'No details held by DVLA';
    case NoResultsReturned = 'No results returned';
    case NotValid = 'Not valid';
    case Valid = 'Valid';
}