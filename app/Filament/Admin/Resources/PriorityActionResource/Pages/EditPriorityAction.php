<?php

namespace App\Filament\Admin\Resources\PriorityActionResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Admin\Resources\PriorityActionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;

class EditPriorityAction extends EditRecord
{
    protected static string $resource = PriorityActionResource::class;

    public function getHeading(): string|Htmlable
    {
        return 'Priority Action ' . $this->getRecord()->id;
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
