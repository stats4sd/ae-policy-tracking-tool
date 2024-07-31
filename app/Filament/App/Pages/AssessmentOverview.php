<?php

namespace App\Filament\App\Pages;

use App\Models\Assessment;
use App\Models\AssessmentPriorityAction;
use App\Services\HelperService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;

class AssessmentOverview extends Page
{

    protected static string $view = 'filament.app.pages.assessment-overview';

    public ?Assessment $assessment;
    public ?Collection $assessmentPriorityActions;
    public string $activeTab = 'tab1';

    public function __construct()
    {
        $this->assessment = HelperService::getCurrentTenant();
        $this->assessmentPriorityActions = $this->assessment?->assessmentPriorityActions;
    }


    protected function getHeaderActions(): array
    {
        return [
            Action::make('finalise')
                ->label('Mark as finalised')
                ->color('success')
                ->visible(Filament::getTenant()->status === 'In Progress')
                ->action(function () {

                    $record = Filament::getTenant();

                    if ($record->status === 'In Progress') {
                        $record->status = 'Finalised';
                        $record->finalised_at = Carbon::now();
                        $record->save();
                    }
                }),
        ];
    }

    public function getHeading(): string
    {

        return 'Assessment for ' . $this->assessment->country->name;
    }

    public function getSubheading(): ?string
    {
        return (new Carbon($this->assessment->created_at))->format('Y-m-d') . "    | " . $this->assessment->status;
    }

}
