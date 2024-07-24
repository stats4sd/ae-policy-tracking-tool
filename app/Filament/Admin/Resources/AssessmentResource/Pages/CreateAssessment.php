<?php

namespace App\Filament\Admin\Resources\AssessmentResource\Pages;

use App\Filament\Admin\Resources\AssessmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAssessment extends CreateRecord
{
    protected static string $resource = AssessmentResource::class;

    protected static bool $canCreateAnother = false;
}
