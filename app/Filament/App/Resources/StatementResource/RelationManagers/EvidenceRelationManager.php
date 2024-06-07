<?php

namespace App\Filament\App\Resources\StatementResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Evidence;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\App\Resources\EvidenceResource;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class EvidenceRelationManager extends RelationManager
{
    protected static string $relationship = 'evidence';

    public function form(Form $form): Form
    {
        return $form
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
            ->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('evidence')
            ->columns([
                Tables\Columns\TextColumn::make('evidence'),
                Tables\Columns\IconColumn::make('official_source')
                                ->boolean()
                                ->sortable(),
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
