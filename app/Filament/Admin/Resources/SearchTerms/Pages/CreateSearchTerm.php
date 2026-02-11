<?php

namespace App\Filament\Admin\Resources\SearchTerms\Pages;

use App\Filament\Admin\Resources\SearchTerms\SearchTermResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSearchTerm extends CreateRecord
{
    protected static string $resource = SearchTermResource::class;
}
