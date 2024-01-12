<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Type;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Statement;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\StatementResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\StatementResource\RelationManagers;

class StatementResource extends Resource
{
    protected static ?string $model = Statement::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type_id')
                                ->label('Type')
                                ->options(Type::all()->pluck('name','id')->toArray())
                                ->required(),  
                Forms\Components\Textarea::make('name')
                                ->rows(4)
                                ->label('Statement')
                                ->required(),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('assessmentPriorityAction.assessment.country.name')
                                ->sortable(),
                Tables\Columns\TextColumn::make('assessmentPriorityAction.assessment_id')
                                ->label('Assessment id')
                                ->sortable(),
                Tables\Columns\TextColumn::make('assessmentPriorityAction.priority_action_id')
                                ->label('Priority action id')
                                ->sortable(),
                Tables\Columns\TextColumn::make('type.name')
                                ->sortable()
                                ->wrap(),
                Tables\Columns\TextColumn::make('name')
                                ->wrap()
                                ->label('Statement'),
                Tables\Columns\TextColumn::make('evidence_count')
                                ->counts('evidence')
                                ->sortable()
                                ->label('# Evidence'),
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
            RelationManagers\EvidenceRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStatements::route('/'),
            'create' => Pages\CreateStatement::route('/create'),
            'edit' => Pages\EditStatement::route('/{record}/edit'),
        ];
    }    
}
