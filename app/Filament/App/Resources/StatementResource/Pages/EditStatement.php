<?php

namespace App\Filament\App\Resources\StatementResource\Pages;

use App\Filament\Admin\Resources\AssessmentResource;
use App\Filament\App\Resources\AssessmentPriorityActionResource;
use App\Filament\App\Resources\StatementResource\StatementResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditStatement extends EditRecord
{
    protected static string $resource = StatementResource::class;

    public function getBreadcrumbs(): array
    {
        $breadcrumbs = [];

        $breadcrumbs['/assessments'] = 'Assessments';

        $statement = $this->getRecord();

        $breadcrumbs[AssessmentResource::getUrl('view', ['record' => $statement->assessmentPriorityAction->assessment])] = $statement->assessmentPriorityAction->assessment->country->name.' '.substr($statement->assessmentPriorityAction->assessment->created_at, 0, stripos($statement->assessmentPriorityAction->assessment->created_at, ' '));

        $breadcrumbs[AssessmentPriorityActionResource::getUrl('view', ['record' => $statement->assessmentPriorityAction])] = 'Priority Action '.$statement->assessmentPriorityAction->priority_action_id;

        return $breadcrumbs;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('delete_statement')
                ->label('Delete')
                ->action(function (): void {
                    $assessment_priority_action_id = $this->record->assessment_priority_action_id;
                    $this->record->delete();
                    $this->redirectRoute('filament.app.resources.assessment-priority-actions.view', ['record' => $assessment_priority_action_id]);
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Statement deleted')
                ),
        ];
    }
}
