<?php

namespace App\Filament\Resources\AssessmentPriorityActionResource\Pages;

use App\Filament\Resources\AssessmentPriorityActionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAssessmentPriorityActions extends ListRecords
{
    protected static string $resource = AssessmentPriorityActionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
