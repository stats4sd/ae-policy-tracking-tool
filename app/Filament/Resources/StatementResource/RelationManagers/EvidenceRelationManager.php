<?php

namespace App\Filament\Resources\StatementResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Evidence;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\EvidenceResource;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class EvidenceRelationManager extends RelationManager
{
    protected static string $relationship = 'evidence';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('evidence')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Checkbox::make('official_source')
                                    ->label('Does this evidence come from an official source?'),
                Forms\Components\FileUpload::make('files')->multiple(),
            ])
            ->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('evidence')
            ->columns([
                Tables\Columns\TextColumn::make('evidence'),
                Tables\Columns\IconColumn::make('official_source')
                                ->boolean()
                                ->sortable()
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
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
            -> recordUrl(fn(Evidence $record) => EvidenceResource::getUrl('edit', ['record' => $record]));
    }
}
