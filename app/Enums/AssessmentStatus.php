<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AssessmentStatus: string implements HasColor, HasLabel
{
    case InProgress = 'In Progress';
    case Review = 'Review';
    case Finalised = 'Finalised';
    case Dropped = 'Dropped';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::InProgress => 'In Progress',
            self::Review => 'Review',
            self::Finalised => 'Finalised',
            self::Dropped => 'Dropped',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::InProgress => 'info',
            self::Review => 'warning',
            self::Finalised => 'success',
            self::Dropped => 'danger',
        };
    }
}
