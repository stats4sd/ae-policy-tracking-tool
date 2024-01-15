<?php

namespace App\Filament\Resources\EvidenceResource\Pages;

use App\Filament\Resources\EvidenceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEvidence extends CreateRecord
{
    protected static string $resource = EvidenceResource::class;
    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string 
    {
        return $this->getResource()::getUrl('index');
    }
    
}
