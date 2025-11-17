<?php

namespace App\Filament\Admin\Resources\AssessmentResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Admin\Resources\AssessmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAssessments extends ListRecords
{
    protected static string $resource = AssessmentResource::class;

    protected static ?string $title = 'Assessments';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }
}
