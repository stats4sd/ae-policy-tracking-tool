<?php

namespace App\Filament\App\Pages;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class RegisterAssessment extends RegisterTenant
{
    // protected string $view = 'filament.app.pages.register-assessment';
    protected Width|string|null $maxContentWidth = Width::FiveExtraLarge;

    public static function getLabel(): string
    {
        return 'Start New Assessment';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->schema([
                Wizard::make([
                    Step::make('1. Assessment Details')
                        ->description('Confirm the key information about the assessment')
                        ->schema([
                            Section::make('')
                                ->schema([
                                    Text::make('Please confirm the details of your assessment below. You can edit these details at any time from the Assessment settings page.'),
                                ])
                                ->columns(1),
                            TextInput::make('title')
                                ->label('Assessment Title')
                                ->required()
                                ->columnSpan(1),
                            Select::make('country_id')
                                ->relationship('country', 'name')
                                ->label('Country')
                                ->createOptionForm([
                                    TextInput::make('name')->label('Country Name')->required(),
                                ])
                                ->searchable()
                                ->preload()
                                ->required()
                                ->columnSpan(1),
                            Select::make('language_id')
                                ->relationship('language', 'name')
                                ->label('Language')
                                ->createOptionForm([
                                    TextInput::make('id')->label('Enter the 2-letter ISO code for the language')
                                        ->unique()
                                        ->required(),
                                    TextInput::make('name')->label('Language Name')->required(),
                                ]),
                            TextInput::make('year')
                                ->label('Year of the Assessment')
                                ->helperText('If the assessment is being conducted over multiple years, enter the starting year.')
                                ->numeric()
                                ->columnSpan(1),
                        ]),
                    Step::make('2. Team Members')
                        ->description('Invite additional people to collaborate')
                        ->schema([
                            Section::make('')
                                ->schema([
                                    Text::make('This tool is intended to be used collaboratively. If you would like to invite other team members to join this assessment, please enter their email addresses below. They will receive an email invitation with instructions on how to join the assessment. You can add team members later from the Assessment settings page if you prefer.'),
                                ])
                                ->columns(1),
                            Repeater::make('invites')
                                ->relationship('invites')
                                ->table([
                                    TableColumn::make('email'),
                                    TableColumn::make('status'),
                                ])
                                ->schema([
                                    TextInput::make('email')
                                        ->required(),
                                    Hidden::make('inviter_id')->default(auth()->id()),
                                ])
                                ->defaultItems(0)
                                ->label('Add Team Members by Email Address'),
                        ]),
                ])->submitAction(new HtmlString(Blade::render(<<<'BLADE'
                        <x-filament::button
                            type="submit"
                            size="sm"
                        >
                            Start New Assessment
                        </x-filament::button>
                        BLADE
                ))),
            ]);
    }

    public function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler('register')
            // override to remove foo
            ->footer([]);
    }
}
