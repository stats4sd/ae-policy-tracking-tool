<?php

namespace App\Filament\App\Resources;

use Filament\Actions\Action;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use App\Models\Highlight;
use Filament\Tables\Table;
use App\Models\PolicyDocument;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\App\Resources\PolicyDocumentResource\Pages;
use App\Filament\App\Resources\PolicyDocumentResource\RelationManagers;

class PolicyDocumentResource extends Resource
{
    protected static ?string $model = PolicyDocument::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Policy Documents';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                    Forms\Components\Section::make('Information')
                        ->columnSpan(1)
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->maxLength(400),
                            Forms\Components\Textarea::make('comments')
                                ->rows(5),
                        ]),

                    // file upload component to show files that can be deleted (files without any highlight)
                    Forms\Components\Section::make('Document')
                        ->columnSpan(1)
                        ->schema([
                            Forms\Components\SpatieMediaLibraryFileUpload::make('editable_documents')
                                ->label('Upload Policy Document(s)')
                                ->hint('If you have the policy document(s), please upload them here.')
                                ->multiple()
                                ->reorderable()
                                ->downloadable()
                                ->preserveFilenames()
                                // restrict file types to pdf, MS Word, text file
                                ->acceptedFileTypes([
                                    'application/pdf',
                                    'application/x-pdf',
                                    'application/msword',
                                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                    'text/plain',
                                    ])
                                // keep this file upload component enabled, so that user can delete the uploaded file
                                ->collection('policy-documents'),
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
                    ->url(fn(PolicyDocument $record) => $record->url)
                    ->searchable(),
                Tables\Columns\TextColumn::make('comments')
                    ->limit(200)
                    ->searchable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('review')
                    ->label('Review Highlights')
                    ->url(fn (PolicyDocument $record): string => static::getUrl('review', ['record' => $record]))
                    ->icon('heroicon-o-eye'),
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
            'index' => Pages\ListPolicyDocuments::route('/'),
            'create' => Pages\CreatePolicyDocument::route('/create'),
            'edit' => Pages\EditPolicyDocument::route('/{record}/edit'),
            'review' => Pages\ReviewPolicyDocument::route('/{record}/review')
        ];
    }
}
