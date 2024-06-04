<?php

namespace App\Filament\App\Resources\AssessmentResource\Pages;

use App\Filament\App\Resources\AssessmentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAssessment extends CreateRecord
{
    protected static string $resource = AssessmentResource::class;

    protected static bool $canCreateAnother = false;
}
