<?php

declare(strict_types=1);

namespace Kerrigan\Dvla\Enums;

enum TaxStatus: string
{
    case NotTaxedForOnRoadUse = 'Not Taxed for on Road Use';
    case SORN = 'SORN';
    case Taxed = 'Taxed';
    case Untaxed = 'Untaxed';
}