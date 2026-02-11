<?php

namespace App\Filament\Admin\Resources\PriorityActions\Pages;

use App\Filament\Admin\Resources\PriorityActions\PriorityActionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewPriorityAction extends ViewRecord
{
    protected static string $resource = PriorityActionResource::class;

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->code_and_short_name;
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
