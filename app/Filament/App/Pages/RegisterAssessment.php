<?php

namespace App\Filament\App\Pages;

use App\Enums\JurisdictionType;
use App\Models\Country;
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
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
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
                            Select::make('jurisdiction_id')
                                ->relationship('jurisdiction', 'name')
                                ->label('Jurisdiction')
                                ->createOptionForm(static::jurisdictionCreateOptionForm())
                                ->searchable()
                                ->preload()
                                ->columnSpan(1)
                                ->required(),
                            Select::make('language_id')
                                ->relationship('language', 'name')
                                ->label('Language')
                                ->createOptionForm([
                                    TextInput::make('id')->label('Enter the 2-letter ISO code for the language')
                                        ->unique()
                                        ->required(),
                                    TextInput::make('name')->label('Language Name')->required(),
                                ])
                                ->required(),
                            TextInput::make('year')
                                ->label('Year of the Assessment')
                                ->helperText('If the assessment is being conducted over multiple years, enter the starting year.')
                                ->numeric()
                                ->minValue(1900)
                                ->maxValue(now()->year + 20)
                                ->validationMessages([
                                    'numeric' => 'The year must be a number.',
                                    'max_value' => 'The year must not be more than 20 years in the future.',
                                    'min_value' => 'The year must not be less than 1900.',
                                ])
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

    /** @return array<int, mixed> */
    public static function jurisdictionCreateOptionForm(): array
    {
        $updateName = static function (Get $get, Set $set): void {
            $type = $get('type');
            $country = ($id = $get('country_id')) ? Country::find($id) : null;
            $subnationalName = $get('subnational_jurisdiction_name');

            if ($type === JurisdictionType::National && $country) {
                $set('name', $country->name.' (National)');
            } elseif ($type === JurisdictionType::Subnational) {
                $set('name', trim(implode(' - ', array_filter([$country?->name, $subnationalName]))));
            } else {
                $set('name', '');
            }
        };

        $isNationalOrSubnational = fn (Get $get): bool => in_array(
            $get('type'),
            [JurisdictionType::National, JurisdictionType::Subnational]
        );

        $isMultiNational = fn (Get $get): bool => $get('type') === JurisdictionType::MultinationalInternational;

        return [
            Select::make('type')
                ->label('Type')
                ->options(JurisdictionType::class)
                ->required()
                ->live()
                ->afterStateUpdated($updateName),
            Select::make('country_id')
                ->label('Country')
                ->relationship('country', 'name')
                ->searchable()
                ->preload()
                ->disabled($isMultiNational)
                ->required($isNationalOrSubnational)
                ->live()
                ->afterStateUpdated($updateName),
            TextInput::make('subnational_jurisdiction_name')
                ->label('Sub-national Jurisdiction Name')
                ->visible(fn (Get $get): bool => $get('type') === JurisdictionType::Subnational)
                ->required(fn (Get $get): bool => $get('type') === JurisdictionType::Subnational)
                ->live()
                ->afterStateUpdated($updateName)
                ->dehydrated(false),
            TextInput::make('name')
                ->live()
                ->label('Jurisdiction Name')
                ->required()
                ->readOnly($isNationalOrSubnational),
        ];
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
