<?php

namespace App\Filament\Resources\AssessmentResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Models\AssessmentPriorityAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;
use App\Filament\Resources\AssessmentPriorityActionResource;

class AssessmentPriorityActionsRelationManager extends RelationManager
{
    protected static string $relationship = 'assessmentPriorityActions';

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
                // Tables\Columns\TextColumn::make('priority_action_id')->sortable(),
                // Tables\Columns\TextColumn::make('priorityAction.name')->wrap(),
                Tables\Columns\TextColumn::make('priority_action_id')
                                ->label('Priority Action')
                                ->description(fn (AssessmentPriorityAction $record): string => $record->priorityAction->name)
                                ->sortable(),
                Tables\Columns\TextColumn::make('statements_count')
                                    ->counts('statements')
                                    ->sortable()
                                    ->label('# Statements'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            -> recordUrl(fn(AssessmentPriorityAction $record) => AssessmentPriorityActionResource::getUrl('edit', ['record' => $record])
        );
    }
}
