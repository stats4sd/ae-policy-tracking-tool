<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssessmentPriorityActionResource\Pages;
use App\Filament\Resources\AssessmentPriorityActionResource\RelationManagers;
use App\Models\AssessmentPriorityAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AssessmentPriorityActionResource extends Resource
{
    protected static ?string $model = AssessmentPriorityAction::class;

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
                Tables\Columns\TextColumn::make('assessment_id'),
                Tables\Columns\TextColumn::make('priority_action_id'),
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
            //
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
