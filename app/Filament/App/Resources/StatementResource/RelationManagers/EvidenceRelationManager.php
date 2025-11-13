<?php

namespace App\Filament\App\Resources\StatementResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Forms;
use Filament\Tables;
use App\Models\Evidence;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\App\Resources\EvidenceResource;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class EvidenceRelationManager extends RelationManager
{
    protected static string $relationship = 'evidence';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
            Textarea::make('evidence')
                            ->required()
                            ->label('Evidence description')
                            ->maxLength(400)
                            ->rows(7),
                        SpatieMediaLibraryFileUpload::make('files')
                            ->multiple()
                            ->reorderable()
                            ->preserveFilenames()
                            ->collection('evidence-files'),
                        Toggle::make('official_source')
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
                TextColumn::make('evidence'),
                IconColumn::make('official_source')
                                ->boolean()
                                ->sortable(),
                TextColumn::make('files'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                 DeleteAction::make(),
            ])
            ->toolbarActions([
                //
            ])
            ->emptyStateActions([
                CreateAction::make(),
            ]);
    }
}
