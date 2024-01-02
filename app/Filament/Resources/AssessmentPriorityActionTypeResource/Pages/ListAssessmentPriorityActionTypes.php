<?php

namespace App\Filament\Resources\AssessmentPriorityActionTypeResource\Pages;

use App\Filament\Resources\AssessmentPriorityActionTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAssessmentPriorityActionTypes extends ListRecords
{
    protected static string $resource = AssessmentPriorityActionTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
