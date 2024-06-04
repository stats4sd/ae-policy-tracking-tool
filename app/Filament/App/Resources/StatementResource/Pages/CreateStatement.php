<?php

namespace App\Filament\App\Resources\StatementResource\Pages;

use App\Filament\App\Resources\StatementResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateStatement extends CreateRecord
{
    protected static string $resource = StatementResource::class;
    protected static bool $canCreateAnother = false;
}
