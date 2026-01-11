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
                    Action::make('export-pdf')
                        ->label('Export as PDF'),
//                        ->url(route('export.pdf'))
//                        ->openInNewTab(),//
                    Action::make('export-excel')
                        ->label('Export data to Excel')
                        ->action(function () {

                            return Excel::download(new \App\Exports\AssessmentExport(Filament::getTenant()), 'assessment.xlsx');

                        }),

                ]),
            ]);
    }
}
