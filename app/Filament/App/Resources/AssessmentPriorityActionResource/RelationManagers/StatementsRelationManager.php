<?php

namespace App\Filament\App\Resources\AssessmentPriorityActionResource\RelationManagers;

use Filament\Forms;
use App\Models\Type;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Statement;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\App\Resources\StatementResource;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class StatementsRelationManager extends RelationManager
{
    protected static string $relationship = 'statements';
    protected static ?string $inverseRelationship = 'assessmentPriorityAction';

    public function isReadOnly(): bool
    {
        return false;
    }

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
                Forms\Components\Repeater::make('evidence_repeater')
                    ->schema([
                        Forms\Components\Textarea::make('evidence')
                            ->required()
                            ->label('Evidence description')
                            ->maxLength(400)
                            ->rows(7),
                        Forms\Components\SpatieMediaLibraryFileUpload::make('files')
                            ->multiple()
                            ->reorderable()
                            ->preserveFilenames()
                            ->collection('evidence-files'),
                        Forms\Components\Toggle::make('official_source')
                            ->inline(false)
                            ->offIcon('heroicon-m-x-mark')
                            ->onIcon('heroicon-m-check')
                    ])
                    ->defaultItems(0)
                    ->addActionLabel('Add new evidence to support this statement')
                    ->columns(3)
//                    ->label('')
                    ->reorderable(false)
                    ->relationship('evidence')
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
//                 Tables\Actions\EditAction::make(),
//                 Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                //
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            -> recordUrl(fn(Statement $record) => StatementResource::getUrl('edit', ['record' => $record]));
    }
}
