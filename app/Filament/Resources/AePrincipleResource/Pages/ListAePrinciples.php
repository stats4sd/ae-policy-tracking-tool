<?php

namespace App\Filament\Resources\AePrincipleResource\Pages;

use App\Filament\Resources\AePrincipleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAePrinciples extends ListRecords
{
    protected static string $resource = AePrincipleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
