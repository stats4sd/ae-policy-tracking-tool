<?php

namespace App\Filament\Resources\AePrincipleResource\Pages;

use App\Filament\Resources\AePrincipleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAePrinciples extends ListRecords
{
    protected static string $resource = AePrincipleResource::class;

    public function getBreadcrumbs(): array
    {
        return [];
    }
}
