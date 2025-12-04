<?php

namespace App\Filament\App\Pages;

use App\Models\Assessment;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class SetupPage extends Page implements HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected string $view = 'filament.app.pages.setup-page';

    protected static ?string $navigationLabel = '0. Setup';

    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected ?string $heading = 'Setup Your Assessment';

    public ?array $formData = [];

    public function mount(): void
    {
        $this->form->fill($this->getRecord()->toArray());
    }

    public function getRecord(): Assessment
    {
        /** @var Assessment $assessment */
        $assessment = Filament::getTenant();

        return $assessment;
    }

    public function form(Schema $schema): Schema
    {

        return $schema
            ->model($this->getRecord())
            ->statePath('formData')
            ->columns(1)
            ->schema([
                Wizard::make([
                    Step::make('1. Assessment Details')
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
                            Select::make('country')
                                ->relationship('country', 'name')
                                ->label('Country')
                                ->createOptionForm([
                                    TextInput::make('name')->label('Country Name')->required(),
                                ])
                                ->searchable()
                                ->preload()
                                ->required()
                                ->columnSpan(1),
                            TextInput::make('year')
                                ->label('Year of the Assessment')
                                ->helperText('If the assessment is being conducted over multiple years, enter the starting year.')
                                ->numeric()
                                ->columnSpan(1),
                        ]),
                    Step::make('2. Team Members')
                        ->schema([
                            Section::make('')
                                ->schema([
                                    Text::make('The list below shows the team members who currently have access to this assessment. Please add any additional email addresses below. They will receive an invitation to join the platform and access this assessment.'),
                                ])
                                ->columns(1),
                            RepeatableEntry::make('users')
                                ->table([
                                    TableColumn::make('name'),
                                    TableColumn::make('email'),
                                    // TODO: add role column (if we implement roles); Add last login column (if we implement user activity tracking)
                                ])
                                ->schema([
                                    TextEntry::make('name'),
                                    TextEntry::make('email'),
                                ]),
                            Repeater::make('invites')
                                ->relationship('invites')
                                ->table([
                                    TableColumn::make('email'),
                                ])
                                ->schema([
                                    TextInput::make('email'),
                                    Hidden::make('inviter_id')->default(auth()->id()),
                                ])
                                ->label('Add Team Members by Email Address'),
                        ]),
                ])->submitAction(new HtmlString('<button wire:click="saveForm" type="submit" class="filament-button filament-button-primary">Save Assessment Setup</button>')),
            ]);
    }

    public function saveForm()
    {
        $data = $this->form->getState();

        $assessment = $this->getRecord();
        $assessment->update([
            'title' => $data['title'],
            'country_id' => $data['country'],
            'year' => $data['year'],
        ]);
    }

}
