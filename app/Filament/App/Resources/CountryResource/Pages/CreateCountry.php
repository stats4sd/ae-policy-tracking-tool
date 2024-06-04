<?php

namespace App\Filament\App\Resources\CountryResource\Pages;

use App\Filament\App\Resources\CountryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCountry extends CreateRecord
{
    protected static string $resource = CountryResource::class;
    protected static bool $canCreateAnother = false;
}
