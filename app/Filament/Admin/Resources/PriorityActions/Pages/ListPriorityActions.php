<?php

namespace App\Filament\Admin\Resources\PriorityActions\Pages;

use App\Filament\Admin\Resources\PriorityActions\PriorityActionResource;
use Filament\Resources\Pages\ListRecords;

class ListPriorityActions extends ListRecords
{
    protected static string $resource = PriorityActionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

}
