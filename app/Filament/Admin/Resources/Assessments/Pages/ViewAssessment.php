<?php

namespace App\Filament\Admin\Resources\Assessments\Pages;

use App\Enums\AssessmentStatus;
use App\Filament\Admin\Resources\Assessments\AssessmentResource;
use App\Models\Assessment;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
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
            Action::make('finalise')
                ->label('Mark as finalised')
                ->color('success')
                ->visible(fn (Assessment $record) => $record->status === AssessmentStatus::InProgress)
                ->action(function (Assessment $record) {
                    if ($record->status === AssessmentStatus::InProgress) {
                        $record->status = AssessmentStatus::Finalised;
                        $record->finalised_at = Carbon::now();
                        $record->save();
                    }
                }),
            DeleteAction::make(),
        ];
    }

    public function getHeading(): string
    {
        $assessment = $this->getRecord();

        return __(($assessment->jurisdiction?->name ?? $assessment->title ?? '').' '.substr($assessment->created_at, 0, stripos($assessment->created_at, ' ')));
    }

    public function getSubheading(): ?string
    {
        return __($this->getRecord()->status->value);
    }
}
