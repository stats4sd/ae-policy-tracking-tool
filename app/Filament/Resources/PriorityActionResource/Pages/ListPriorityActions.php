<?php

namespace App\Filament\Resources\PriorityActionResource\Pages;

use App\Filament\Resources\PriorityActionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPriorityActions extends ListRecords
{
    protected static string $resource = PriorityActionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
