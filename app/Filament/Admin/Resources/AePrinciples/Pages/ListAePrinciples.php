<?php

namespace App\Filament\Admin\Resources\AePrinciples\Pages;

use App\Filament\Admin\Resources\AePrinciples\AePrincipleResource;
use Filament\Resources\Pages\ListRecords;

class ListAePrinciples extends ListRecords
{
    protected static string $resource = AePrincipleResource::class;

    public function getBreadcrumbs(): array
    {
        return [];
    }
}
