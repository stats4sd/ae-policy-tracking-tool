<?php

namespace App\Filament\App\Resources\AssessmentResource\RelationManagers;

use App\Models\AssessmentPriorityAction;
use App\Models\PriorityAction;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class PoliciesRelationManager extends RelationManager
{
    protected static string $relationship = 'policies';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('assessmentPriorityActions')
                    ->label('Priority Actions')
                    ->relationship('assessmentPriorityActions', 'id')
                    ->options(function () {
                        $assessment_id = $this->getOwnerRecord()->id;
                        $results = AssessmentPriorityAction::where('assessment_id', $assessment_id)
                                    ->join('priority_actions', 'assessment_priority_action.priority_action_id', '=', 'priority_actions.id')
                                    ->select(
                                        'assessment_priority_action.id as assessment_priority_action_id',
                                        'priority_actions.id as priority_action_id',
                                        'priority_actions.name'
                                    )
                                    ->get();
                        $options = [];
                        foreach ($results as $result) {
                            $options[$result->assessment_priority_action_id] = "{$result->priority_action_id} - {$result->name}";
                        }
                        return $options;
                    })
                    ->placeholder('Tag any relevant priority actions')
                    ->multiple()
                    ->preload()
                    ->loadingMessage('Loading priority actions...')
                    ->noSearchResultsMessage('No priority actions match your search')
                    ->searchable(),
                Forms\Components\TextInput::make('comments')
                    ->maxLength(400),
                Forms\Components\SpatieMediaLibraryFileUpload::make('files')
                    ->multiple()
                    ->reorderable()
                    ->preserveFilenames()
                    ->collection('evidence-files'),
            ])->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('assessmentPriorityActions.priority_action_id')
                    ->label('Priority Actions')
                    ->listWithLineBreaks()
                    ->badge(),
                Tables\Columns\TextColumn::make('comments')->wrap(),
                Tables\Columns\TextColumn::make('files'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                //
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
}
