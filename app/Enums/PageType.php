<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PageType: string implements HasColor, HasLabel
{
    case Title = 'title';
    case Contents = 'contents';
    case Body = 'body';
    case Unknown = 'unknown';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Title => 'Title',
            self::Contents => 'Contents',
            self::Body => 'Body',
            self::Unknown => 'Unknown',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Title => 'warning',
            self::Contents => 'warning',
            self::Body => 'success',
            self::Unknown => 'gray',
        };
    }
}
