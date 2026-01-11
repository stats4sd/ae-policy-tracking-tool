<?php

namespace App\Filament\App\Pages;

use Awcodes\Shout\Components\Shout;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Maatwebsite\Excel\Facades\Excel;

class Summary extends Page
{
    protected string $view = 'filament.app.pages.summary';

    protected static ?string $navigationLabel = '4. Summary';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCheck;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::DocumentCheck;

    public function summaryInfoList(Schema $schema): Schema
    {
        return $schema
            ->components([
                Shout::make('Note')
                    ->heading('Summary Page In Development')
                    ->type('info')
                    ->content('This summary page is currently under development and will be available in a future release. Currently, you may export the assessment data below.'),
                Actions::make([
                    Action::make('export-excel')
                        ->label('Export data to Excel')
                        ->action(function () {

                            $filename = Filament::getTenant()->title.' - export.xlsx';

                            return Excel::download(new \App\Exports\AssessmentDataExport\AssessmentExport(Filament::getTenant()), $filename);

                        }),
                    Action::make('export-document-summary')
                        ->label('Export Document Summary By Recommendation')
                        ->action(function () {

                            $filename = Filament::getTenant()->title.' - document summary.docx';

                            return Excel::download(new \App\Exports\DocumentSummaryExport\DocumentSummaryExport(Filament::getTenant()), $filename);
                        }),

                    Action::make('export-highlights')
                        ->label('Export Highlights')
                        ->action(function () {

                            $filename = Filament::getTenant()->title.' - highlights.xlsx';

                            return Excel::download(new \App\Exports\HighlightByRecommendationExport(Filament::getTenant()), $filename);
                        }),
                ]),
            ]);
    }
}
