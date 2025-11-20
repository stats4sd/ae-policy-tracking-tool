<?php

namespace App\Filament\App\Resources\Highlights\Tables;

use App\Models\Highlight;
use Awcodes\Shout\Components\Shout;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class HighlightsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->paginationPageOptions([25, 50, 100, 200])
            ->defaultPaginationPageOption(50)
            ->groups([
                Group::make('policy_document_id')
                    ->label('Policy Document')
                    ->getTitleFromRecordUsing(fn ($record) => $record->policyDocument->name ?? 'No Document')
                    ->titlePrefixedWithLabel(false)
                    ->collapsible(),
            ])
            ->columns([
                TextColumn::make('priorityActions.id')
                    ->label('Priority Action'),
                TextColumn::make('extract')
                    ->searchable()
                    ->limitWithTooltip(),
                TextColumn::make('searchTerms.phrase')
                    ->badge()
                    ->label('Auto-Matched keywords'),
                IconColumn::make('verified')
                    ->boolean(),
            ])
            ->filtersTriggerAction(fn (Action $action) => $action->hiddenLabel(false)->link()->label('Filters'))
            ->deferFilters(false)
            ->filtersLayout(FiltersLayout::Modal)
            ->filters([
                TernaryFilter::make('verified')
//                    ->default(true)
                    ->label('Show Verified Highlights')
                    ->trueLabel('Only Verified')
                    ->falseLabel('Only Unverified'),
                SelectFilter::make('searchTerms.phrase')
                    ->label('Matched Autosearch Tearms')
                    ->relationship('searchTerms', 'phrase')
                    ->multiple(),
            ])
            ->recordActions([
                Action::make('Verify Highlight')
                    ->visible(fn (Highlight $record) => ! $record->verified)
                    ->requiresConfirmation()
                    ->modalDescription('This highlight was created by an automated search. Are you sure you want to verify it as relevant to this assessment?')
                    ->action(function (Highlight $record) {
                        $record->verified = true;
                        $record->save();
                    }),
                Action::make('Add Summary Statement')
                    ->visible(fn (Highlight $record) => $record->verified)
                    ->schema(function (Collection $selectedRecords) {
                        return [
                            Shout::make('info')
                                ->content('Highlighted extracts can be grouped and summarised into a single summary statement. The summary statements can then be included in the final report.')
                                ->icon('heroicon-o-information-circle'),
                            RepeatableEntry::make('selected_highlights')
                                ->table([
                                    TextColumn::make('priorityActions.id'),
                                    TextColumn::make('extract')->label('Highlight Extract')->wrap(),
                                ]),
                            Textarea::make('name')
                                ->label('Enter Summary Statement')
                                ->rows(3),
                        ];
                    })
                    ->action(function ($record, array $data) {
                        dd($data);
                    }),
                DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->toolbarActions([
                BulkAction::make('Summarise')
                    ->fillForm(function (Collection $selectedRecords) {

                        // TODO: organise selected highlights by source document
                        return [
                            'selected_highlights' => $selectedRecords->map(fn ($record) => $record->extract)->toArray(),
                            'name' => 'aosidjfoasidfjoasidjf'
                        ];
                    })
                    ->schema([
                        Shout::make('info')
                            ->content('Highlighted extracts can be grouped and summarised into a single summary statement. The summary statements can then be included in the final report.')
                            ->icon('heroicon-o-information-circle'),
                        Repeater::make('selected_highlights')
                            ->reorderable(false)
                            ->addable(false)
                            ->deletable(false)
                            ->simple(Textarea::make('extract')->disabled()->autosize()),
                        Textarea::make('name')
                            ->label('Enter Summary Statement')
                            ->rows(3),
                    ])
                    ->action(function ($record, array $data) {
                        dd($data);
                    }),
            ]);
    }
}
