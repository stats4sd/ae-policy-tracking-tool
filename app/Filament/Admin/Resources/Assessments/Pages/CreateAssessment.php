<?php

namespace App\Filament\Admin\Resources\Assessments\Pages;

use App\Filament\Admin\Resources\Assessments\AssessmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAssessment extends CreateRecord
{
    protected static string $resource = AssessmentResource::class;

    protected static bool $canCreateAnother = false;
}
