<?php

namespace App\Filament\Admin\Resources\SearchTermResource\Pages;

use App\Filament\Admin\Resources\SearchTermResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSearchTerm extends CreateRecord
{
    protected static string $resource = SearchTermResource::class;
}
