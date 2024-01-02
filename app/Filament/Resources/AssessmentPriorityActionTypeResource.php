<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssessmentPriorityActionTypeResource\Pages;
use App\Filament\Resources\AssessmentPriorityActionTypeResource\RelationManagers;
use App\Models\AssessmentPriorityActionType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AssessmentPriorityActionTypeResource extends Resource
{
    protected static ?string $model = AssessmentPriorityActionType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('assessment_priority_action_id'),
                Tables\Columns\TextColumn::make('type.name'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListAssessmentPriorityActionTypes::route('/'),
            'create' => Pages\CreateAssessmentPriorityActionType::route('/create'),
            'edit' => Pages\EditAssessmentPriorityActionType::route('/{record}/edit'),
        ];
    }    
}
