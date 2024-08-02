<?php

namespace App\Filament\App\Resources\AssessmentPriorityActionResource\Pages;

use App\Filament\Admin\Resources\AssessmentResource;
use App\Filament\App\Resources\AssessmentPriorityActionResource;
use Filament\Resources\Pages\ViewRecord;

class ViewAssessmentPriorityAction extends ViewRecord
{
    protected static string $resource = AssessmentPriorityActionResource::class;

    public function getBreadcrumbs(): array
    {
        $breadcrumbs = [];

        $breadcrumbs['/assessments'] = 'Assessments';

        $assessment_pa = $this->getRecord();

        $breadcrumbs[AssessmentResource::getUrl('view', ['record' => $assessment_pa->assessment])] = $assessment_pa->assessment->country->name . ' ' . substr($assessment_pa->assessment->created_at,0,stripos($assessment_pa->assessment->created_at," "));

        return $breadcrumbs;
    }

    public function getHeading(): string
    {
        $assessment_pa = $this->getRecord();

        return __('Priority Action ' . $assessment_pa->priority_action_id);
    }

    public function getSubheading(): ?string
    {
        return __($this->getRecord()->priorityAction->name);
    }

}
