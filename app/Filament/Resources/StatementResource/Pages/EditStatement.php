<?php

namespace App\Filament\Resources\StatementResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\CountryResource;
use App\Filament\Resources\StatementResource;
use App\Filament\Resources\AssessmentResource;
use App\Filament\Resources\AssessmentPriorityActionResource;

class EditStatement extends EditRecord
{
    protected static string $resource = StatementResource::class;

    public function getBreadcrumbs(): array
    {
        $breadcrumbs = [];

        $statement = $this->getRecord();

        $breadcrumbs[CountryResource::getUrl('edit', ['record' => $statement->assessmentPriorityAction->assessment->country])] = $statement->assessmentPriorityAction->assessment->country->name;

        $breadcrumbs[AssessmentResource::getUrl('edit', ['record' => $statement->assessmentPriorityAction->assessment])] = 'Assessment ' . substr($statement->assessmentPriorityAction->assessment->created_at,0,stripos($statement->assessmentPriorityAction->assessment->created_at," "));

        $breadcrumbs[AssessmentPriorityActionResource::getUrl('edit', ['record' => $statement->assessmentPriorityAction])] = 'Priority Action ' . $statement->assessmentPriorityAction->priority_action_id;

        $breadcrumbs[StatementResource::getUrl('edit', ['record' => $statement])] = 'Statement';

        return $breadcrumbs;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
