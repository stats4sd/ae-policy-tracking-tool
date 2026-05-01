<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum JurisdictionType: string implements HasLabel
{
    case National = 'National';
    case Subnational = 'Subnational';
    case MultinationalInternational = 'Multinational / International';

    public function getLabel(): ?string
    {
        return $this->value;
    }
}
