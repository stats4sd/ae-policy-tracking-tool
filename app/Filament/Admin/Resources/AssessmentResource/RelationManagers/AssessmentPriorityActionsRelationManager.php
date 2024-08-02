<?php

namespace App\Filament\Admin\Resources\AssessmentResource\RelationManagers;

use App\Filament\App\Resources\AssessmentPriorityActionResource;
use App\Models\AssessmentPriorityAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AssessmentPriorityActionsRelationManager extends RelationManager
{
    protected static string $relationship = 'assessmentPriorityActions';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('priority_action_id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('priority_action_id')
            ->columns([
                Tables\Columns\TextColumn::make('priority_action_id')->sortable(),
                Tables\Columns\TextColumn::make('priorityAction')
                                        ->formatStateUsing(fn ($state): string => $state->name)
                                        ->wrap(),
                Tables\Columns\TextColumn::make('policies.name')
                                        ->listWithLineBreaks()
                                        ->badge(),
                Tables\Columns\TextColumn::make('statements_count')
                                        ->counts('statements')
                                        ->sortable()
                                        ->label('# Statements'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ])
            ->emptyStateActions([
                //
            ])
            -> recordUrl(fn(AssessmentPriorityAction $record) => AssessmentPriorityActionResource::getUrl('view', ['record' => $record])
        );
    }
}
