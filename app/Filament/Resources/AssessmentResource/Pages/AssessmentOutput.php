<?php

namespace App\Filament\Resources\AssessmentResource\Pages;

use App\Filament\Resources\AssessmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class AssessmentOutput extends ViewRecord
{
    protected static string $resource = AssessmentResource::class;

    public function getTitle(): string|Htmlable
    {
        return $this->record->country->name . ' - Assessment ' . substr($this->record->created_at,0,stripos($this->record->created_at," "));
    }

    protected static string $view = 'assessments.output';

}
