<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\PolicyResource\Pages;
use App\Filament\App\Resources\PolicyResource\RelationManagers;
use App\Models\Policy;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PolicyResource extends Resource
{
    protected static ?string $model = Policy::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Policy Documents';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('test')
                        ->form([
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->maxLength(255)
                                ->label('Policy Name'),
                        ])
                        ->action(function (array $data): void {
                             dd($data);
                        }),
                    ]),
                    Forms\Components\Section::make('Information')
                        ->columnSpan(1)
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->maxLength(400),
                            Forms\Components\Textarea::make('comments')
                                ->rows(5),
                        ]),
                    Forms\Components\Section::make('Documents')
                        ->columnSpan(1)
                        ->schema([
                            Forms\Components\SpatieMediaLibraryFileUpload::make('documents')
                                ->label('Upload Policy Document(s)')
                                ->hint('If you have the policy document(s), please upload them here.')
                                ->multiple()
                                ->reorderable()
                                ->preserveFilenames()
                                ->collection('policy-documents'),
                            Forms\Components\TextInput::make('url')
                                ->label('URL to Policy Document(s)')
                                ->hint('If you do not have the policy document(s), please provide the URL to the document(s) online'),
                        ]),

                ])
                    ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('url')
                    ->url(fn(Policy $record) => $record->url)
                    ->searchable(),
                Tables\Columns\TextColumn::make('comments')
                    ->limit(200)
                    ->searchable(),

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
            'index' => Pages\ListPolicies::route('/'),
            'create' => Pages\CreatePolicy::route('/create'),
            'edit' => Pages\EditPolicy::route('/{record}/edit'),
        ];
    }
}
