<?php

namespace App\Filament\App\Resources\Extracts\Tables;

use App\Filament\App\Resources\Extracts\Pages\ListExtracts;
use App\Models\Extract;
use App\Models\PriorityAction;
use App\Models\Statement;
use App\Models\Theme;
use Awcodes\Shout\Components\Shout;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class ExtractTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading(fn ($livewire) => 'Extracts for Priority Action: '.$livewire->activeTab)
            ->paginationPageOptions([25, 50, 100, 200])
            ->defaultPaginationPageOption(50)
            ->columns([
                IconColumn::make('verified')
                    ->label('')
                    ->boolean(),
                TextColumn::make('extract')
                    ->searchable()
                    // macro setup in DefStudio\FilamentColumnLengthLimiter package
                    ->limitWithTooltip()
                    ->description(fn (Extract $record): HtmlString => new HtmlString('<span class="text-xs"> From document: <b>'.$record->policyDocument->name.' (page '.$record->page_number.' of '.$record->policyDocument->pages->count().' )</b></span>' ?? 'No Document'))
                    ->label('Highlighted Extract')
                    ->extraHeaderAttributes(['style' => 'min-width: 300px; width: 50vw;'])
                    ->extraAttributes(['style' => 'min-width: 300px; width: 50vw;']),
                TextColumn::make('priorityActions.id')
                    ->toggleable()
                    ->badge()
                    ->wrap()
                    ->label('Priority Actions'),
                TextColumn::make('theme.name')
                    ->toggleable()
                    ->label('Theme')
                    ->wrap(),
                TextColumn::make('score.score')
                    ->toggleable()
                    ->badge()
                    ->color(fn (Extract $record) => match ($record->score->score ?? null) {
                        null => 'secondary',
                        2 => 'success',
                        -1 => 'danger',
                        default => 'info',
                    })
                    ->tooltip(fn (Extract $record) => $record->score->name ?? 'No Score Assigned')
                    ->label('Score'),

            ])
            ->filtersTriggerAction(fn (Action $action) => $action->hiddenLabel(false)->link()->label('Filters'))
            ->deferFilters(false)
            ->filtersLayout(FiltersLayout::AboveContent)
            ->filters([
                TernaryFilter::make('verified')
//                    ->default(true)
                    ->label('Show Verified Extracts')
                    ->trueLabel('Only Verified')
                    ->falseLabel('Only Unverified'),
                SelectFilter::make('searchTerms.phrase')
                    ->label('Matched Autosearch Terms')
                    ->relationship('searchTerms', 'phrase')
                    ->multiple(),
                SelectFilter::make('policy_document_id')
                    ->label('Source Document')
                    ->relationship('policyDocument', 'name')
                    ->multiple(),
                SelectFilter::make('theme_id')
                    ->label('Theme')
                    ->relationship('theme', 'name', function ($query, $livewire) {
                        $query->where('assessment_id', Filament::getTenant()->id)
                            ->where('priority_action_id', $livewire->activeTab);
                    })
                    ->multiple(),

            ])
            ->recordActions([
                Action::make('Verify Extract')
                    ->label('Edit + Verify')
                    ->icon(Heroicon::OutlinedPencilSquare)
                    ->schema([
                        Select::make('score_id')
                            ->relationship('score', 'name')
                            ->required(),
                        Select::make('theme_id')
                            ->relationship('theme', 'name'),
                        ToggleButtons::make('verified')
                            ->boolean(trueLabel: 'Verified', falseLabel: 'Unverified')
                            ->grouped()
                            ->label('Verification Status')
                            ->helperText('Confirm that this automated extract is relevant to the assessment.'),

                    ])
                    ->modalDescription('Edit the scoring')
                    ->action(function (Extract $record, array $data) {
                        $record->verified = $data['verified'];
                        $record->score_id = $data['score_id'];
                        $record->save();
                    }),
                DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->toolbarActions([
                BulkAction::make('Summarise')
                    ->fillForm(fn (Collection $selectedRecords) => [
                        'selected_extracts' => $selectedRecords->map(fn ($record) => $record->extract)->toArray(),
                    ])
                    ->schema(function (Collection $selectedRecords, ListExtracts $livewire) {
                        $priorityAction = PriorityAction::firstWhere('id', $livewire->activeTab);

                        return [
                            Shout::make('priority_action_info')
                                ->content('This summary statement will be assigned to Priority Action: '.($priorityAction?->code_and_short_name ?? $livewire->activeTab).'.')
                                ->icon('heroicon-o-information-circle'),
                            Section::make('Selected Extracts ('.$selectedRecords->count().')')
                                ->collapsible()
                                ->collapsed()
                                ->schema([
                                    Repeater::make('selected_extracts')
                                        ->reorderable(false)
                                        ->addable(false)
                                        ->deletable(false)
                                        ->simple(Textarea::make('extract')->disabled()->autosize()),
                                ]),
                            Select::make('theme_id')
                                ->relationship('theme', 'name', function ($query) use ($livewire) {
                                    $query->where('assessment_id', Filament::getTenant()->id)
                                        ->where('priority_action_id', $livewire->activeTab);
                                })
                                ->label('Which theme does this statement relate to?')
                                ->createOptionForm([
                                    TextInput::make('name')->required()->label('Enter the new theme'),
                                ])
                                ->createOptionUsing(function (array $data, ListExtracts $livewire) {
                                    $data['assessment_id'] = Filament::getTenant()->id;
                                    $data['priority_action_id'] = $livewire->activeTab;

                                    $theme = Theme::create($data);
                                    $livewire->dispatch('refreshTable');

                                    return $theme->id;
                                })
                                ->required(),
                            Textarea::make('name')
                                ->label('Enter Summary Statement')
                                ->required()
                                ->rows(3),
                        ];
                    })
                    ->action(function (Collection $selectedRecords, array $data, ListExtracts $livewire) {
                        $statement = Statement::create([
                            'name' => $data['name'],
                            'priority_action_id' => $livewire->activeTab,
                            'theme_id' => $data['theme_id'],
                            'assessment_id' => Filament::getTenant()->id,
                        ]);

                        $statement->extracts()->sync($selectedRecords->pluck('id'));

                        $livewire->dispatch('refreshTable');
                    }),
                BulkAction::make('Assign to Theme')
                    ->schema(function (ListExtracts $livewire) {
                        return [
                            Shout::make('info')
                                ->content('Link the selected extracts to a theme for Priority Action: '.$livewire->activeTab.'. This will help organise highlights and summary statements under relevant themes for reporting.')
                                ->icon('heroicon-o-information-circle'),
                            Select::make('theme_id')
                                ->label('Select Theme to Assign')
                                ->options(function (ListExtracts $livewire) {
                                    return Theme::query()
                                        ->where('assessment_id', Filament::getTenant()->id)
                                        ->where('priority_action_id', $livewire->activeTab)
                                        ->pluck('name', 'id');
                                })
                                ->searchable()
                                ->createOptionForm([
                                    TextInput::make('name')->required()->label('Theme name'),
                                ])
                                ->createOptionUsing(function (array $data, ListExtracts $livewire) {
                                    $data['assessment_id'] = Filament::getTenant()->id;
                                    $data['priority_action_id'] = $livewire->activeTab;

                                    return Theme::create($data)->id;
                                }),
                        ];
                    })
                    ->action(function (Collection $selectedRecords, array $data, ListExtracts $livewire) {
                        foreach ($selectedRecords as $extract) {
                            $extract->priorityActions()->syncWithoutDetaching([$livewire->activeTab]);
                            $extract->theme_id = $data['theme_id'] ?? null;
                            $extract->save();
                        }

                        $livewire->dispatch('refreshTable');
                    }),
            ]);
    }
}
