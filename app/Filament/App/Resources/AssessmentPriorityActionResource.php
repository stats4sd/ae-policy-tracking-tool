<?php

namespace App\Filament\App\Resources;

use App\Models\Policy;
use Filament\Forms\Components;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\AssessmentPriorityAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\App\Resources\AssessmentPriorityActionResource\Pages;
use App\Filament\App\Resources\AssessmentPriorityActionResource\RelationManagers;

class AssessmentPriorityActionResource extends Resource
{
    protected static ?string $model = AssessmentPriorityAction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('policies_id')
                                    ->label('Linked Policies')
                                    ->columnSpan(2)
                                    ->multiple()
                                    ->relationship('policies', 'name')
                                    ->allowHtml()
                                    ->getOptionLabelFromRecordUsing(fn (Policy $record) => "{$record->name} <br>{$record->comments}")
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
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
            'view' => Pages\ViewAssessmentPriorityAction::route('/{record}/view'),
        ];
    }
}
