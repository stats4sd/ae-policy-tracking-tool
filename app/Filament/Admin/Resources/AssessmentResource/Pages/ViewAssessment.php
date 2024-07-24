<?php

namespace App\Filament\Admin\Resources\AssessmentResource\Pages;

use App\Filament\Admin\Resources\AssessmentResource;
use App\Models\Assessment;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAssessment extends ViewRecord
{
    protected static string $resource = AssessmentResource::class;

    public function getBreadcrumbs(): array
    {
        $breadcrumbs = [];

        $breadcrumbs['/assessments'] = 'Assessments';

        return $breadcrumbs;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('finalise')
                                ->label('Mark as finalised')
                                ->color('success')
                                ->visible(fn(Assessment $record) => $record->status === 'In Progress')
                                ->action(function (Assessment $record) {
                                    if($record->status==='In Progress') {
                                        $record->status = 'Finalised';
                                        $record->finalised_at = Carbon::now();
                                        $record->save();
                                    }
                                }),
            Actions\DeleteAction::make(),
        ];
    }

    public function getHeading(): string
    {
        $assessment = $this->getRecord();

        return __($assessment->country->name . ' ' . substr($assessment->created_at,0,stripos($assessment->created_at," ")));
    }

    public function getSubheading(): ?string
    {
        return __($this->getRecord()->status);
    }
}
