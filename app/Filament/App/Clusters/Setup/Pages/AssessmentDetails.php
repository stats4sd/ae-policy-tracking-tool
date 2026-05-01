<?php

namespace App\Filament\App\Clusters\Setup\Pages;

use App\Filament\App\Clusters\Setup\SetupCluster;
use App\Filament\App\Pages\RegisterAssessment;
use App\Models\Assessment;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * @property-read Schema $form
 */
class AssessmentDetails extends Page
{
    protected static ?string $cluster = SetupCluster::class;

    protected string $view = 'filament.app.pages.assessment-details';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCheck;

    protected static ?int $navigationSort = 1;

    /** @var array<string, mixed> | null */
    public ?array $data = [];

    protected static ?string $title = 'Assessment Details';

    public function mount(): void
    {
        $this->form->fill($this->getRecord()?->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->record($this->getRecord())
            ->statePath('data')
            ->components([
                Form::make([
                    Section::make('')
                        ->schema([
                            Text::make('Please confirm the details of your assessment below.'),
                        ])
                        ->columns(1),
                    TextInput::make('title')
                        ->label('Assessment Title')
                        ->required()
                        ->columnSpan(1),
                    Select::make('jurisdiction_id')
                        ->relationship('jurisdiction', 'name')
                        ->label('Jurisdiction')
                        ->createOptionForm(RegisterAssessment::jurisdictionCreateOptionForm())
                        ->searchable()
                        ->preload()
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
                    Textarea::make('description')
                        ->label('Description')
                        ->helperText('Optional notes or context about this assessment.')
                        ->rows(4)
                        ->columnSpan(1),
                    TextInput::make('year')
                        ->label('Year of the Assessment')
                        ->helperText('If the assessment is being conducted over multiple years, enter the starting year.')
                        ->numeric()
                        ->columnSpan(1),
                ])
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('submit')
                                ->label('Save Assessment Details')
                                ->button()
                                ->keyBindings(['mod+s'])
                                ->submit('save'),
                        ]),
                    ]),
            ]);
    }

    public function save(): void
    {
        $this->form->getState();
        $record = $this->getRecord();

        if ($record) {
            $record->update($this->data ?? []);
        }

        Notification::make()
            ->success()
            ->title('Assessment details saved successfully.')
            ->send();
    }

    public function getRecord(): ?Assessment
    {
        /** @var Assessment|null $assessment */
        $assessment = Filament::getTenant();

        return $assessment;
    }
}
