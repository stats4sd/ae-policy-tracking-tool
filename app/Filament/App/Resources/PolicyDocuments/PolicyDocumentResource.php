<?php

namespace App\Filament\App\Resources\PolicyDocuments;

use App\Filament\App\Resources\PolicyDocuments\Pages\BulkUploadPage;
use App\Filament\App\Resources\PolicyDocuments\Pages\CreatePolicyDocument;
use App\Filament\App\Resources\PolicyDocuments\Pages\EditPolicyDocument;
use App\Filament\App\Resources\PolicyDocuments\Pages\ListPolicyDocuments;
use App\Filament\App\Resources\PolicyDocuments\Pages\ReviewPolicyDocument;
use App\Models\Language;
use App\Models\PolicyDocument;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class PolicyDocumentResource extends Resource
{
    protected static ?string $model = PolicyDocument::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedMagnifyingGlassPlus;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::MagnifyingGlassPlus;

    protected static ?string $navigationLabel = '1. Search Documents';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Information')
                    ->columnSpan(1)
                    ->schema([
                        TextInput::make('name')
                            ->label('Enter the full name of the document')
                            ->required()
                            ->maxLength(400),
                        TextInput::make('short_title')
                            ->label('Enter a short title for easy reference')
                            ->helperText('This will be used to identify the document in lists and summaries.')
                            ->required()
                            ->maxLength(200),
                        Textarea::make('comments')
                            ->label('Additional Comments')
                            ->helperText('Any additional information about the document that may be relevant to the assessment.')
                            ->rows(5),
                        Select::make('language_id')
                            ->label('Language')
                            ->helperText('The language this document is written in. Defaults to the assessment language.')
                            ->options(Language::all()->mapWithKeys(fn (Language $lang) => [$lang->id => $lang->getTranslation('name', 'en')]))
                            ->default(fn () => Filament::getTenant()->language_id)
                            ->required(),
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
                    ->wrap()
                    ->searchable(),
                TextColumn::make('language_id')
                    ->label('Language')
                    ->getStateUsing(function (PolicyDocument $record): string {
                        $language = $record->language ?? Filament::getTenant()->language;

                        return $language
                            ? $language->getTranslation('name', 'en').($record->language_id ? '' : ' (assessment default)')
                            : '—';
                    }),
                TextColumn::make('pages_count')
                    ->label('# Pages')
                    ->counts('pages'),
                TextColumn::make('automatic_extracts_count')
                    ->label(fn () => new HtmlString('# Automatic <br/>Search results'))
                    ->counts('automaticExtracts'),
                TextColumn::make('verified_extracts_count')
                    ->label(fn () => new HtmlString('# Verified<br/> Extracts'))
                    ->counts('verifiedExtracts'),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('review')
                    ->label('Search & Find Extracts')
                    ->url(fn (PolicyDocument $record): string => static::getUrl('review', ['record' => $record]))
                    ->icon('heroicon-o-magnifying-glass'),
                Action::make('redo_search')
                    ->label('Re-run Auto Search')
                    ->icon('heroicon-o-arrow-path')
                    ->requiresConfirmation()
                    ->modalDescription('Re-runs the automatic search for this document. Any existing, unverified extracts will be deleted and new ones will be created.')
                    ->action(fn (PolicyDocument $record) => $record->runAutomaticSearch()),
                EditAction::make(),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalDescription('Any extracts and linked data will be permanently deleted. Do not do this unless you have uploaded the wrong document and need to remove it from the assessment.'),
            ])
            ->toolbarActions([
                BulkAction::make('redo_search')
                    ->label('Re-run auto search')
                    ->tooltip('Re-runs the automatic search for all selected documents. Any existing, unverified extracts will be deleted and new ones will be created. This may take some time depending on the number of documents selected.')
                    ->action(function (BulkAction $action, \Illuminate\Support\Collection $selectedRecords) {
                        foreach ($selectedRecords as $record) {
                            $record->runAutomaticSearch();
                        }
                    }),

                DeleteBulkAction::make(),

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
            'bulk-upload' => BulkUploadPage::route('/bulk-upload'),
        ];
    }
}
