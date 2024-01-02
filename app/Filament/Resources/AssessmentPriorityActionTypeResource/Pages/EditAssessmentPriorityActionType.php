<?php

namespace App\Filament\Resources\AssessmentPriorityActionTypeResource\Pages;

use App\Filament\Resources\AssessmentPriorityActionTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssessmentPriorityActionType extends EditRecord
{
    protected static string $resource = AssessmentPriorityActionTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
