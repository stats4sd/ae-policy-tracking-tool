<?php

namespace App\Filament\Admin\Resources\AePrincipleResource\Pages;

use App\Filament\Admin\Resources\AePrincipleResource;
use Filament\Resources\Pages\ListRecords;

class ListAePrinciples extends ListRecords
{
    protected static string $resource = AePrincipleResource::class;

    public function getBreadcrumbs(): array
    {
        return [];
    }
}
