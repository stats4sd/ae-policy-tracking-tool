<?php

namespace App\Filament\App\Resources\StatementResource\Pages;

use App\Filament\App\Resources\StatementResource\StatementResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStatement extends CreateRecord
{
    protected static string $resource = StatementResource::class;

    protected static bool $canCreateAnother = false;
}
