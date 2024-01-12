<?php

namespace App\Filament\Resources\AssessmentPriorityActionResource\RelationManagers;

use Filament\Forms;
use App\Models\Type;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Statement;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\StatementResource;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class StatementsRelationManager extends RelationManager
{
    protected static string $relationship = 'statements';
    protected static ?string $inverseRelationship = 'assessmentPriorityAction';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type_id')
                    ->label('Type')
                    ->inlineLabel()
                    ->options(Type::all()->pluck('name','id')->toArray())
                    ->required(),  
                Forms\Components\Textarea::make('name')
                    ->label('Statement')
                    ->required()
                    ->rows(4)
                    ->inlineLabel()
                    ->required(),
            ])->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
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
                Tables\Filters\SelectFilter::make('type.name')
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\Action::make('add_evidence')->icon('heroicon-m-plus'),
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
            -> recordUrl(fn(Statement $record) => StatementResource::getUrl('edit', ['record' => $record]));
    }
}
