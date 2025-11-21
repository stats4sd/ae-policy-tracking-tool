<?php

namespace App\Livewire;

use App\Models\PriorityAction;
use App\Models\Theme;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class ThemesForHighlightsTable extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public PriorityAction $priorityAction;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Priority Action: '.$this->priorityAction->name)
            ->relationship(fn () => $this->priorityAction->themes())
            ->columns([
                TextColumn::make('name')->label('Theme Name')->wrap(),
                TextColumn::make('highlights_count')->counts('highlights')->label('Number of Highlights'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->schema([
                        TextInput::make('name')->label('Enter the theme')
                            ->required(),
                        Select::make('priority_action_id')
                            ->relationship('priorityAction', 'code_and_name')
                            ->default($this->priorityAction->id)
                            ->wrapOptionLabels(),
                    ])
                    ->using(fn (array $data) => Theme::create([
                        'name' => $data['name'],
                        'assessment_id' => Filament::getTenant()->id,
                        'priority_action_id' => $data['priority_action_id'],
                    ])),
            ]);
    }

    public function render()
    {
        return view('livewire.themes-for-highlights-table');
    }
}
