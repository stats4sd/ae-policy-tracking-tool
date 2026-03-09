<?php

namespace App\Filament\Admin\Resources\Languages;

use App\Filament\Admin\Resources\Languages\Pages\ListLanguages;
use App\Models\Language;
use Awcodes\Shout\Components\Shout;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class LanguageResource extends Resource
{
    protected static ?string $model = Language::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-language';

    public static function form(Schema $schema): Schema
    {
        $languages = Language::all();

        $nameInputs = $languages->map(fn ($language) => TextInput::make('name.'.$language->id)
            ->label('Name ('.$language->getTranslation('name', 'en').')')
            ->required($language->id === $languages->first()->id)
            ->maxLength(255)
        )->toArray();

        return $schema
            ->components([
                Shout::make('language-management')
                    ->heading('Find the ISO code')
                    ->content(new HtmlString('You can find the ISO code for a language by looking it up on Wikipedia: <a href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes">https://en.wikipedia.org/wiki/List_of_ISO_639_language_codes</a>')),
                TextInput::make('id')
                    ->label('Language code (ISO 639-1)')
                    ->required()
                    ->maxLength(10)
                    ->visibleOn('create'),
                ...$nameInputs,
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->withCount(['assessments', 'policyDocuments']))
            ->columns([
                TextColumn::make('id')
                    ->label('Code')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Name (English)')
                    ->getStateUsing(fn (Language $record) => $record->getTranslation('name', 'en'))
                    ->sortable(),
                TextColumn::make('assessments_count')
                    ->label('Assessments')
                    ->sortable(),
                TextColumn::make('policy_documents_count')
                    ->label('Policy Documents')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->disabled(fn (Language $record) => $record->assessments_count > 0 || $record->policy_documents_count > 0)
                    ->tooltip(fn (Language $record) => $record->assessments_count > 0 || $record->policy_documents_count > 0
                        ? 'Cannot delete a language that is in use by assessments or policy documents.'
                        : null
                    ),
            ])
            ->emptyStateActions([
                CreateAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLanguages::route('/'),
        ];
    }
}
