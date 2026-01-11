<?php

namespace App\Filament\App\Pages;

use App\Models\Assessment;
use App\Models\AssessmentPriorityAction;
use App\Models\PriorityAction;
use App\Models\Recommendation;
use App\Services\HelperService;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
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
use Livewire\Attributes\On;

class AssessmentOverview extends Page
{

    protected string $view = 'filament.app.pages.assessment-overview';

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedDocumentText;
    protected static string | BackedEnum | null $activeNavigationIcon = Heroicon::DocumentText;
    protected static ?string $navigationLabel = '3. Review Statements';

    public ?Assessment $assessment;
    public ?Collection $statementsByType;
    public ?Collection $recommendations;

    public int $activeTab;
    public Recommendation $activeRecommendation;

    public function __construct()
    {
        $this->assessment = HelperService::getCurrentTenant();
        $this->recommendations = Recommendation::with('priorityActions.statements')->get();

        $this->activeTab = 1;
        $this->updateActiveRecommendation();
    }


    protected function getHeaderActions(): array
    {
        return [
            Action::make('ready-for-review')
                ->label('Mark as Ready For Review')
                ->color('info')
                ->outlined()
                ->visible(Filament::getTenant()->status === 'In Progress')
                ->action(function () {

                    $record = Filament::getTenant();

                    if ($record->status === 'In Progress') {
                        $record->status = 'Review';
                        $record->finalised_at = Carbon::now();
                        $record->save();

                        $this->js('window.location.reload()');

                    }
                }),
            Action::make('not-ready')
            ->label('Mark as Not Ready')
            ->color('info')
            ->visible(Filament::getTenant()->status === 'Review')
            ->action(function () {

                $record = Filament::getTenant();

                if ($record->status === 'Review') {
                    $record->status = 'In Progress';
                    $record->finalised_at = null;
                    $record->save();

                    $this->js('window.location.reload()');
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

    #[On('tabChanged')]
    public function setActiveTab(int $index)
    {
        $this->activeTab = $index;
        $this->updateActiveRecommendation();
    }

    public function updateActiveRecommendation()
    {
        $this->activeRecommendation = $this->recommendations->firstWhere('id', $this->activeTab);
    }

}
