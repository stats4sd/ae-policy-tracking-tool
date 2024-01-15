<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EvidenceResource\Pages;
use App\Filament\Resources\EvidenceResource\RelationManagers;
use App\Models\Evidence;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EvidenceResource extends Resource
{
    protected static ?string $model = Evidence::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('statement_id')
                                    ->placeholder('Select the statement')
                                    ->relationship('statement', 'name')
                                    ->required(),
                Forms\Components\TextInput::make('evidence')->required(),
                Forms\Components\Checkbox::make('official_source')
                                    ->label('Does this evidence come from an official source?'),
                Forms\Components\SpatieMediaLibraryFileUpload::make('files')
                                    ->multiple()
                                    ->reorderable()
                                    ->preserveFilenames()
                                    ->collection('evidence-files'),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('evidence'),
                Tables\Columns\IconColumn::make('official_source')
                                ->boolean()
                                ->sortable()
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
            'index' => Pages\ListEvidence::route('/'),
            'create' => Pages\CreateEvidence::route('/create'),
            'edit' => Pages\EditEvidence::route('/{record}/edit'),
        ];
    }    
}
