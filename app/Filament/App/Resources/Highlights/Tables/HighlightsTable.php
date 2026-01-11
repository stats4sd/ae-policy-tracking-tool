<?php

namespace App\Filament\App\Resources\Highlights\Tables;

use App\Filament\App\Resources\Highlights\Pages\ListHighlights;
use App\Models\Highlight;
use App\Models\Statement;
use App\Models\Type;
use Awcodes\Shout\Components\Shout;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
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
            ->heading(fn ($livewire) => $livewire->activeTab === 'all' ? 'All Highlights' : 'Highlights for Priority Action: '.$livewire->activeTab)
            ->paginationPageOptions([25, 50, 100, 200])
            ->defaultPaginationPageOption(50)
            ->columns([
                TextColumn::make('extract')
                    ->searchable()
                    // macro setup in DefStudio\FilamentColumnLengthLimiter package
                    ->limitWithTooltip()
                    ->description(fn (Highlight $record) => 'From: '.$record->policyDocument->name ?? 'No Document')
                    ->label('Highlighted Extract'),
                TextColumn::make('priorityActions.id')
                    ->toggleable()
                    ->label('Priority Action'),
                TextColumn::make('theme.name')
                    ->toggleable()
                    ->label('Theme')
                    ->wrap(),

                TextColumn::make('searchTerms.phrase')
                    ->toggleable()
                    ->badge()
                    ->label('keywords'),
                TextColumn::make('type.score')
                    ->toggleable()
                    ->badge()
                    ->color(fn (Highlight $record) => match ($record->type->score ?? null) {
                        null => 'secondary',
                        2 => 'success',
                        -1 => 'danger',
                        default => 'info',
                    })
                    ->tooltip(fn (Highlight $record) => $record->type->name ?? 'No Score Assigned')
                    ->label('Score'),


                IconColumn::make('verified')
                    ->toggleable()
                    ->boolean(),
            ])
            ->filtersTriggerAction(fn (Action $action) => $action->hiddenLabel(false)->link()->label('Filters'))
            ->deferFilters(false)
            ->filtersLayout(FiltersLayout::AboveContent)
            ->filters([
                TernaryFilter::make('verified')
//                    ->default(true)
                    ->label('Show Verified Highlights')
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
                        $query->where('assessment_id', Filament::getTenant()->id);

                        // if not on the 'all' tab, filter themes to only those linked to the active priority action
                        if ($livewire->activeTab !== 'all') {
                            $query->where('priority_action_id', $livewire->activeTab);
                        }

                    })
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
                DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->toolbarActions([
                BulkAction::make('Summarise')
                    ->fillForm(function (Collection $selectedRecords) {
                        // TODO: organise selected highlights by source document
                        return [
                            'selected_highlights' => $selectedRecords->map(fn ($record) => $record->extract)->toArray(),
                            'default_priority_action_id' => $selectedRecords->flatMap->priorityActions->first()->id ?? null,
                        ];
                    })
                    ->schema(fn (Collection $selectedRecords) => [
                        Shout::make('info')
                            ->content('Highlighted extracts can be grouped and summarised into a single summary statement. The summary statements can then be included in the final report.')
                            ->icon('heroicon-o-information-circle'),
                        Repeater::make('selected_highlights')
                            ->reorderable(false)
                            ->addable(false)
                            ->deletable(false)
                            ->simple(Textarea::make('extract')->disabled()->autosize()),

                        // If all the highlights are for a single priority action, show a read-only field with that action id
                        Shout::make('priority_action_info')
                            ->visible(fn () => $selectedRecords->flatMap->priorityActions->unique('id')->count() === 1)
                            ->content(fn () => 'All selected highlights are linked to Priority Action: '.$selectedRecords->flatMap->priorityActions->first()->id.'. The summary statement will be assigned to this action.'),

                        // If the highlights are for multiple priority actions, show a select to choose which one to assign the statement to
                        Select::make('priority_action_id')
                            ->visible(fn () => $selectedRecords->flatMap->priorityActions->unique('id')->count() > 1)
                            ->options($selectedRecords->flatMap->priorityActions->unique('id')->pluck('id', 'id')->toArray())
                            ->label('Assign Priority Action')
                            ->required(),

                        // If all highlights are for a single priority action, set a hidden field with that action id
                        Hidden::make('default_priority_action_id'),

                        Select::make('theme_id')
                            ->relationship('theme', 'name', function ($query) use ($selectedRecords) {
                                $query->where('assessment_id', Filament::getTenant()->id);

                                if ($selectedRecords->flatMap->priorityActions->isNotEmpty()) {
                                    $query
                                        ->where('priority_action_id', $selectedRecords->flatMap->priorityActions->first()->id ?? null);
                                }

                                return $query;
                            })
                            ->label('Which theme does this statement relate to?')
                            ->createOptionForm([
                                TextInput::make('name')->required()->label('Enter the new theme'),
                            ])
                            ->createOptionUsing(function (array $data, Get $get, ListHighlights $livewire) {

                                $priorityActionId = $get('default_priority_action_id') ?? $get('priority_action_id');

                                $data['assessment_id'] = Filament::getTenant()->id;
                                $data['priority_action_id'] = $priorityActionId;

                                $theme = \App\Models\Theme::create($data);

                                $livewire->dispatch('refreshTable');

                                return $theme->id;

                            })
                            ->required(),

                        // Summary statement input
                        Textarea::make('name')
                            ->label('Enter Summary Statement')
                            ->required()
                            ->rows(3),

                        Radio::make('type_id')
                            ->options(Type::all()->mapWithKeys(fn (Type $type) => [$type->id => '( '.$type->score.' ) '.$type->name])->toArray())
                            ->label('How does this statement link to the priority action? (Select the most appropriate type)')
                            ->required(),
                    ])
                    ->action(function (Collection $selectedRecords, array $data, ListHighlights $livewire) {

                        $priorityActionId = $data['priority_action_id'] ?? $data['default_priority_action_id'];

                        $statement = Statement::create([
                            'name' => $data['name'],
                            'type_id' => $data['type_id'],
                            'priority_action_id' => $priorityActionId,
                            'theme_id' => $data['theme_id'],
                        ]);

                        $statement->highlights()->sync($selectedRecords->pluck('id'));

                        $livewire->dispatch('refreshTable');

                    }),
                BulkAction::make('Assign to Theme')
                    ->schema(fn (Collection $selectedRecords) => [
                        Shout::make('info')
                            ->content('Link the selected highlights to a theme. This will help organise highlights and summary statements under relevant themes for reporting.')
                            ->icon('heroicon-o-information-circle'),
                        Select::make('theme_id')
                            ->relationship('theme', 'name', function ($query) use ($selectedRecords) {
                                $query->where('assessment_id', Filament::getTenant()->id);
                                if ($selectedRecords->flatMap->priorityActions->isNotEmpty()) {
                                    $query
                                        ->where('priority_action_id', $selectedRecords->flatMap->priorityActions->first()->id ?? null);
                                }

                                return $query;
                            })
                            ->createOptionForm([
                                TextInput::make('name')->required()->label('Enter the new theme'),
                            ])
                            ->createOptionUsing(function (array $data, Get $get, ListHighlights $livewire) {

                                $priorityActionId = $get('default_priority_action_id') ?? $get('priority_action_id');

                                $data['assessment_id'] = Filament::getTenant()->id;
                                $data['priority_action_id'] = $priorityActionId;

                                $theme = \App\Models\Theme::create($data);

                                $livewire->dispatch('refreshTable');

                                return $theme->id;

                            })
                            ->label('Select Theme to Assign'),
                    ])
                    ->action(function (Collection $selectedRecords, array $data, ListHighlights $livewire) {

                        foreach ($selectedRecords as $highlight) {
                            $highlight->theme_id = $data['theme_id'];
                            $highlight->save();
                        }

                        $livewire->dispatch('refreshTable');

                    }),
            ]);
    }
}
