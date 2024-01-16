<?php

namespace App\Filament\Resources\AssessmentResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\CountryResource;
use App\Filament\Resources\AssessmentResource;

class EditAssessment extends EditRecord
{
    protected static string $resource = AssessmentResource::class;

    public function getBreadcrumbs(): array
    {
        $breadcrumbs = [];

        $assessment = $this->getRecord();

        $breadcrumbs[CountryResource::getUrl('edit', ['record' => $assessment->country])] = $assessment->country->name;

        $breadcrumbs[AssessmentResource::getUrl('edit', ['record' => $assessment])] = 'Assessment ' . substr($assessment->created_at,0,stripos($assessment->created_at," "));

        return $breadcrumbs;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
