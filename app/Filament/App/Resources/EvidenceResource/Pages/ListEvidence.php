<?php

namespace App\Filament\App\Resources\EvidenceResource\Pages;

use App\Filament\App\Resources\EvidenceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEvidence extends ListRecords
{
    protected static string $resource = EvidenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
