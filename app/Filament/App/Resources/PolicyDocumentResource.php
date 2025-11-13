<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\PolicyDocumentResource\Pages\CreatePolicyDocument;
use App\Filament\App\Resources\PolicyDocumentResource\Pages\EditPolicyDocument;
use App\Filament\App\Resources\PolicyDocumentResource\Pages\ListPolicyDocuments;
use App\Filament\App\Resources\PolicyDocumentResource\Pages\ReviewPolicyDocument;
use App\Models\PolicyDocument;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PolicyDocumentResource extends Resource
{
    protected static ?string $model = PolicyDocument::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Policy Documents';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Information')
                    ->columnSpan(1)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(400),
                        Textarea::make('comments')
                            ->rows(5),
                    ]),

                // file upload component to show files that can be deleted (files without any highlight)
                Section::make('Document')
                    ->columnSpan(1)
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('editable_documents')
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
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('url')
                    ->url(fn (PolicyDocument $record) => $record->url)
                    ->searchable(),
                TextColumn::make('comments')
                    ->limit(200)
                    ->searchable(),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('review')
                    ->label('Review Highlights')
                    ->url(fn (PolicyDocument $record): string => static::getUrl('review', ['record' => $record]))
                    ->icon('heroicon-o-eye'),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ListPolicyDocuments::route('/'),
            'create' => CreatePolicyDocument::route('/create'),
            'edit' => EditPolicyDocument::route('/{record}/edit'),
            'review' => ReviewPolicyDocument::route('/{record}/review'),
        ];
    }
}
