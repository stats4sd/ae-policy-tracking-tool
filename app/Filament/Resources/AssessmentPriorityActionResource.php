<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\AssessmentPriorityAction;
use Illuminate\Database\Eloquent\Builder;
use RelationManagers\StatementsRelationManager;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AssessmentPriorityActionResource\Pages;
use App\Filament\Resources\AssessmentPriorityActionResource\RelationManagers;

class AssessmentPriorityActionResource extends Resource
{
    protected static ?string $model = AssessmentPriorityAction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('priority_action_id')->disabledOn('edit'),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('assessment.country.name')->sortable(),
                Tables\Columns\TextColumn::make('assessment_id')->sortable(),
                Tables\Columns\TextColumn::make('priority_action_id')->sortable(),
                Tables\Columns\TextColumn::make('statements_count')
                                ->counts('statements')
                                ->sortable()
                                ->label('# Statements'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            RelationManagers\StatementsRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssessmentPriorityActions::route('/'),
            'create' => Pages\CreateAssessmentPriorityAction::route('/create'),
            'edit' => Pages\EditAssessmentPriorityAction::route('/{record}/edit'),
        ];
    }    
}
