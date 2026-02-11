<?php

namespace App\Filament\Admin\Resources\Recommendations\RelationManagers;

use App\Filament\Admin\Resources\PriorityActions\PriorityActionResource;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class PriorityActionsRelationManager extends RelationManager
{
    protected static string $relationship = 'priorityActions';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->label('Code')
                    ->helperText('E.g. 1.1, 1.2, 2.1, etc.')
                    ->unique()
                    ->required()
                    ->disabledOn('edit'),
                Forms\Components\TextInput::make('short_name'),
                Forms\Components\Textarea::make('name')
                    ->label('Long Name / Description')
                    ->rows(4),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('short_name')
                    ->wrap(),
                Tables\Columns\TextColumn::make('search_terms_count')
                    ->label('# Search Terms')
                    ->counts('searchTerms'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                Action::make('View')
                    ->label('View + Update Search Terms')
                    ->url(fn($record) => PriorityActionResource::getUrl('view', ['record' => $record->id])),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->groupedBulkActions([
        BulkActionGroup::make([
            DeleteBulkAction::make(),
        ]),
    ]);
    }
}
