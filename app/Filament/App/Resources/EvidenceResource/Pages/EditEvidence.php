<?php

namespace App\Filament\App\Resources\EvidenceResource\Pages;

use App\Filament\Admin\Resources\AssessmentResource;
use App\Filament\App\Resources\AssessmentPriorityActionResource;
use App\Filament\App\Resources\CountryResource;
use App\Filament\App\Resources\EvidenceResource;
use App\Filament\App\Resources\StatementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEvidence extends EditRecord
{
    protected static string $resource = EvidenceResource::class;

    public function getBreadcrumbs(): array
    {
        $breadcrumbs = [];

        $breadcrumbs['/assessments'] = 'Assessments';

        $evidence = $this->getRecord();

        $breadcrumbs[AssessmentResource::getUrl('edit', ['record' => $evidence->statement->assessmentPriorityAction->assessment])] = $evidence->statement->assessmentPriorityAction->assessment->country->name . ' ' . substr($evidence->statement->assessmentPriorityAction->assessment->created_at,0,stripos($evidence->statement->assessmentPriorityAction->assessment->created_at," "));

        $breadcrumbs[AssessmentPriorityActionResource::getUrl('edit', ['record' => $evidence->statement->assessmentPriorityAction])] = 'Priority Action ' . $evidence->statement->assessmentPriorityAction->priority_action_id;

        $breadcrumbs[StatementResource::getUrl('edit', ['record' => $evidence->statement])] = 'Statement';

        $breadcrumbs[EvidenceResource::getUrl('edit', ['record' => $evidence])] = 'Evidence';

        return $breadcrumbs;
    }

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
