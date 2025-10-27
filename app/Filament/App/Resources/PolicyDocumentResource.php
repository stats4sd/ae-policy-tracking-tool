<?php

namespace App\Filament\App\Resources;

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

                    // the origninal file upload component
                    // it will be replaced by another two file upload components later
                    // TODO: remove this file upload component when finish testing Editable Documents and Non Editable Documents file upload components
                    // Forms\Components\Section::make('Documents')
                    //     ->columnSpan(1)
                    //     ->schema([
                    //         Forms\Components\SpatieMediaLibraryFileUpload::make('documents')
                    //             ->label('Upload Policy Document(s)')
                    //             ->hint('If you have the policy document(s), please upload them here.')
                    //             ->multiple()
                    //             ->reorderable()
                    //             ->preserveFilenames()
                    //             ->collection('policy-documents'),
                    //         Forms\Components\TextInput::make('url')
                    //             ->label('URL to Policy Document(s)')
                    //             ->hint('If you do not have the policy document(s), please provide the URL to the document(s) online'),
                    //     ]),

                    // file upload component to show files that can be deleted (files without any highlight)
                    Forms\Components\Section::make('Editable Documents')
                        ->columnSpan(1)
                        ->schema([
                            Forms\Components\SpatieMediaLibraryFileUpload::make('editable_documents')
                                ->label('Upload Policy Document(s)')
                                ->hint('If you have the policy document(s), please upload them here.')
                                ->multiple()
                                ->reorderable()
                                ->downloadable()
                                ->preserveFilenames()
                                // keep this file upload component enabled, so that user can delete the uploaded file
                                ->filterMediaUsing(
                                    function (Collection $media, Get $get) {
                                        // find distinct media id existed in highlights table
                                        $mediaIds = Highlight::select('media_id')->distinct()->get()->pluck('media_id');

                                        // add filter to include media without any highlight
                                        $filteredMedia = $media->whereNotIn('id', $mediaIds);

                                        return $filteredMedia;
                                    }
                                )
                                ->collection('policy-documents'),
                        ]),

                    // file upload component to show files that cannot be deleted (files with any highlight)
                    Forms\Components\Section::make('Non Editable Documents')
                        ->columnSpan(1)
                        ->schema([
                            Forms\Components\SpatieMediaLibraryFileUpload::make('non_editable_documents')
                                ->label('Uploaded Policy Document(s)')
                                ->hint('There are highlights in these uploaded documents, therefore they cannot be deleted')
                                ->multiple()
                                ->reorderable()
                                ->downloadable()
                                ->preserveFilenames()
                                // keep this file upload component disabled, so that user cannot delete the uploaded file
                                ->disabled()
                                ->filterMediaUsing(
                                    function (Collection $media, Get $get) {
                                        // find distinct media id existed in highlights table
                                        $mediaIds = Highlight::select('media_id')->distinct()->get()->pluck('media_id');

                                        // add filter to include media with highlights
                                        $filteredMedia = $media->whereIn('id', $mediaIds);

                                        return $filteredMedia;
                                    }
                                )
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
        ];
    }
}
