<?php

namespace App\Filament\App\Pages;

use App\Exports\AssessmentDataExport\AssessmentExport;
use App\Exports\DocumentSummaryExport\DocumentSummaryExport;
use App\Exports\ExtractByRecommendationExport;
use App\Support\Dashboard\MockDashboardData;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Livewire\Attributes\Url;
use Maatwebsite\Excel\Facades\Excel;

class Summary extends Page
{
    protected string $view = 'filament.app.pages.summary';

    protected static ?string $navigationLabel = '4. Summary';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCheck;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::DocumentCheck;

    #[Url]
    public bool $prep = false;

    #[Url(as: 'pa')]
    public string $selectedPa = '1.2';

    public array $featured = [0, 1, 2];

    public function selectPa(string $paId): void
    {
        $this->selectedPa = $paId;
    }

    public function toggleFeatured(int $index): void
    {
        if (in_array($index, $this->featured)) {
            $this->featured = array_values(array_diff($this->featured, [$index]));

            return;
        }

        $this->featured[] = $index;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('prep')
                ->label(fn () => $this->prep ? 'Done preparing' : 'Prepare summary')
                ->icon(fn () => $this->prep ? Heroicon::OutlinedCheck : Heroicon::OutlinedStar)
                ->color(fn () => $this->prep ? 'primary' : 'gray')
                ->action(fn () => $this->prep = ! $this->prep),
            ActionGroup::make([
                Action::make('export-excel')
                    ->label('Export data to Excel')
                    ->action(fn () => Excel::download(new AssessmentExport(Filament::getTenant()), Filament::getTenant()->title.' - export.xlsx')),
                Action::make('export-document-summary')
                    ->label('Export document summary by recommendation')
                    ->action(fn () => Excel::download(new DocumentSummaryExport(Filament::getTenant()), Filament::getTenant()->title.' - document summary.xlsx')),
                Action::make('export-extracts')
                    ->label('Export extracts')
                    ->action(fn () => Excel::download(new ExtractByRecommendationExport(Filament::getTenant()), Filament::getTenant()->title.' - extracts.xlsx')),
            ])
                ->label('Export')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('gray')
                ->button(),
        ];
    }

    protected function getViewData(): array
    {
        $priorityActions = collect(MockDashboardData::priorityActions());

        $documents = collect(MockDashboardData::documents());

        $documentYears = $documents->pluck('year')->sort()->values();

        $selected = $priorityActions->firstWhere('id', $this->selectedPa);

        return [
            'assessment' => MockDashboardData::assessment(),
            'scores' => MockDashboardData::scores(),
            'recommendations' => MockDashboardData::recommendations(),
            'priorityActions' => $priorityActions->groupBy('recommendation_id'),
            'selected' => $selected,
            'drillDown' => $this->selectedPa === MockDashboardData::drillDown()['pa'] ? MockDashboardData::drillDown() : null,
            'keyFindings' => MockDashboardData::keyFindings(),
            'documents' => $documents,
            'principles' => MockDashboardData::principles(),
            'heatmap' => MockDashboardData::documentHeatmap(),
            'stats' => [
                'evidenced' => $priorityActions->where('extracts', '>', 0)->count(),
                'total' => $priorityActions->count(),
                'supportive' => $priorityActions->where('direction', 'positive')->count(),
                'mixed' => $priorityActions->where('direction', 'mixed')->count(),
                'perverse' => $priorityActions->filter(fn ($pa) => $pa['dist'][-2] + $pa['dist'][-1] > 0)->count(),
                'medianYear' => $documentYears[intdiv($documentYears->count(), 2)],
                'oldestYear' => $documentYears->first(),
                'newestYear' => $documentYears->last(),
            ],
            'timeline' => ['from' => 1995, 'to' => 2030, 'assessmentYear' => 2025, 'today' => 2026],
        ];
    }
}
