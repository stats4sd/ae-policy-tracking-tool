<?php

namespace App\Filament\Resources\EvidenceResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\CountryResource;
use App\Filament\Resources\EvidenceResource;
use App\Filament\Resources\StatementResource;
use App\Filament\Resources\AssessmentResource;
use App\Filament\Resources\AssessmentPriorityActionResource;

class EditEvidence extends EditRecord
{
    protected static string $resource = EvidenceResource::class;

    public function getBreadcrumbs(): array
    {
        $breadcrumbs = [];

        $evidence = $this->getRecord();

        $breadcrumbs[CountryResource::getUrl('edit', ['record' => $evidence->statement->assessmentPriorityAction->assessment->country])] = $evidence->statement->assessmentPriorityAction->assessment->country->name;

        $breadcrumbs[AssessmentResource::getUrl('edit', ['record' => $evidence->statement->assessmentPriorityAction->assessment])] = 'Assessment ' . substr($evidence->statement->assessmentPriorityAction->assessment->created_at,0,stripos($evidence->statement->assessmentPriorityAction->assessment->created_at," "));

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
