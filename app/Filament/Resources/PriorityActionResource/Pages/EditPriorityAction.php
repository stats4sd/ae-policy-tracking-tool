<?php

namespace App\Filament\Resources\PriorityActionResource\Pages;

use App\Filament\Resources\PriorityActionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPriorityAction extends EditRecord
{
    protected static string $resource = PriorityActionResource::class;

    protected function getRedirectUrl(): string 
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
