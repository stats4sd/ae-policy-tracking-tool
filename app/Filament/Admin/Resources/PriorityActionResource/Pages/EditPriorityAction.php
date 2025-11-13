<?php

namespace App\Filament\Admin\Resources\PriorityActionResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Admin\Resources\PriorityActionResource;
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
            DeleteAction::make(),
        ];
    }
}
