<?php

namespace App\Livewire;

use App\Models\PriorityAction;
use App\Models\Score;
use App\Models\Statement;
use DaveMills\FilamentTableInASchema\TableInSchema;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class StatementEditor extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public Score $type;

    public Collection $statements;

    public PriorityAction $priorityAction;

    public bool $first;

    // # Editing Stuff
    public ?array $data = [];

    public bool $editing = false;

    public function mount(): void
    {
        // $this->form->fill($this->priorityAction->toArray());
    }

    public function render()
    {
        return view('livewire.statement-editor');
    }

    public function table(Table $table): Table
    {
        return $table
            ->relationship(fn () => $this->priorityAction->statements()->where('type_id', $this->type->id))
            ->paginated(false)
            ->defaultGroup(Group::make('theme_id')
                ->label('Theme')
                ->getTitleFromRecordUsing(fn ($record) => $record->theme->name ?? 'No Theme')
            )
            ->columns([
                Grid::make([
                    'default' => 2,
                ])
                    ->schema([
                        TextColumn::make('name')
                            ->label('Statement')
                            ->description(fn (Statement $record): string => 'Linked to '.$record->linked_policy_documents->count().' Policy Document(s)')
                            ->wrap(),
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->schema(fn (Statement $record) => [
                        Section::make('Extracts')
                            ->extraAttributes([
                                'class' => 'compact-section',
                            ])
                            ->heading('Document Extracts Linked to This Statement')
                            ->description('The extracts from policy documents that are linked to this statement are shown below. You can refer to these when editing the statement to ensure it accurately reflects the content of the linked documents.')
                            ->schema([
                                TableInSchema::make()
                                    ->table(fn (Table $table): Table => $table
                                        ->paginated(false)
                                        ->relationship(fn () => $record->extracts())
                                        ->defaultGroup(Group::make('policy_document_id')
                                            ->label('Policy Document')
                                            ->getTitleFromRecordUsing(fn ($record) => $record->policyDocument->name ?? 'No Document')
                                            ->collapsible()
                                        )
                                        ->columns([
                                            Grid::make(1)
                                                ->schema([
                                                    TextColumn::make('extract')->label('Extract')->wrap(),

                                                ]),
                                        ])

                                    ),
                            ])->columns(1),
                        TextArea::make('name')
                            ->label('Enter the statement'),
                        Select::make('theme_id')
                            ->label('Theme')
                            ->relationship('theme', 'name', fn (Builder $query) => $query->where('assessment_id', Filament::getTenant()->id)->where('priority_action_id', $this->priorityAction->id))
                            ->nullable(),
                        Select::make('type_id')
                            ->label('Type')
                            ->default($this->type->id)
                            ->relationship('type', 'name'),
                    ])
                    ->after(fn () => $this->dispatch('refreshStatementEditor')),
                DeleteAction::make(),
            ]);
    }

    #[On('refreshStatementEditor')]
    public function refreshData()
    {
        $this->resetTable();
    }

    public function update(): void
    {
        // add type_id to the data before saving
        // using saveRelationships() only works if all the required fields are in the form. We cannot do this here because we want the "simple" visuals of only having a single input in the repeater. So we have to manually add the type_id to the data before saving.
        $statements = collect($this->data['statements'])->map(function ($statement) {

            // if the statement doesn't already exist; add the type_id and create the entry
            if (! isset($statement['id'])) {
                $statement['type_id'] = $this->type->id;
                $statement['assessment_id'] = Filament::getTenant()->id;

                $this->priorityAction->statements()->create($statement);

                // remove the statement from the relationship list.
                return null;
            }

            return $statement;
        })
            ->filter(fn ($statement) => $statement !== null)
            ->toArray();

        $this->data['statements'] = $statements;

        $this->form->saveRelationships();

        // somehow the statements are saved just by me getting state.

        $this->editing = false;
        $this->priorityAction->refresh();
        $this->form->fill($this->priorityAction->toArray());

    }
}
