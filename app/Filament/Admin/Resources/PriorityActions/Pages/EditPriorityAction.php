<?php

namespace App\Filament\Admin\Resources\PriorityActions\Pages;

use App\Filament\Admin\Resources\PriorityActions\PriorityActionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditPriorityAction extends EditRecord
{
    protected static string $resource = PriorityActionResource::class;

    public function getHeading(): string|Htmlable
    {
        return 'Priority Action '.$this->getRecord()->id;
    }

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
