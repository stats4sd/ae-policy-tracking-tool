<?php

namespace App\Filament\App\Resources\AssessmentPriorityActionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\App\Resources\CountryResource;
use App\Filament\App\Resources\AssessmentResource;
use App\Filament\App\Resources\AssessmentPriorityActionResource;

class EditAssessmentPriorityAction extends EditRecord
{
    protected static string $resource = AssessmentPriorityActionResource::class;

    public function getBreadcrumbs(): array
    {
        $breadcrumbs = [];

        $assessment_pa = $this->getRecord();

        $breadcrumbs[CountryResource::getUrl('edit', ['record' => $assessment_pa->assessment->country])] = $assessment_pa->assessment->country->name;

        $breadcrumbs[AssessmentResource::getUrl('edit', ['record' => $assessment_pa->assessment])] = 'Assessment ' . substr($assessment_pa->assessment->created_at,0,stripos($assessment_pa->assessment->created_at," "));

        $breadcrumbs[AssessmentPriorityActionResource::getUrl('edit', ['record' => $assessment_pa])] = 'Priority Action ' . $assessment_pa->priority_action_id;

        return $breadcrumbs;
    }

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }
}
