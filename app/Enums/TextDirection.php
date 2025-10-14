<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasDescription;

enum TextDirection: string implements HasLabel, HasDescription
{
    // define string values
    case LEFT_TO_RIGHT = 'left_to_right';
    case RIGHT_TO_LEFT = 'right_to_left';

    // define labels
    public function getLabel(): ?string
    {
        return match ($this) {
            self::LEFT_TO_RIGHT => 'Left to right',
            self::RIGHT_TO_LEFT => 'Right to left',
        };
    }

    // define descritpions
    public function getDescription(): ?string
    {
        return match ($this) {
            self::LEFT_TO_RIGHT => 'Text is written and read from the left side of the page or screen to the right',
            self::RIGHT_TO_LEFT => 'Text is written and read from the right side of the page or screen to the left',
        };
    }
}
