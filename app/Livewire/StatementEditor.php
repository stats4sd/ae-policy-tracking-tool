<?php

namespace App\Livewire;

use Filament\Schemas\Schema;
use Filament\Actions\Action;
use App\Filament\Shared\Forms\Components\SimpleRepeaterWithTags;
use App\Filament\Shared\Forms\Components\TextAreaWithTags;
use App\Models\PolicyDocument;
use App\Models\PriorityAction;
use App\Models\Statement;
use App\Models\Type;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Facades\Filament;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Livewire\Component;

class StatementEditor extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public Type $type;

    public Collection $statements;

    public PriorityAction $priorityAction;

    public bool $first;

    // # Editing Stuff
    public ?array $data = [];

    public bool $editing = false;

    public function mount(): void
    {
        $this->form->fill($this->priorityAction->toArray());
    }

    public function render()
    {
        return view('livewire.statement-editor');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                SimpleRepeaterWithTags::make('statements')
                    ->relationship(modifyQueryUsing: function (Builder $query) {
                        $query->where('type_id', $this->type->id);
                    })
                    ->hiddenLabel()
                    ->simple(
                        TextareaWithTags::make('name')
                            ->autosize()
                            ->required()
                            ->hiddenLabel()
                            ->tags(function (TextAreaWithTags $component): array {

                                // extract statement ID from state-path
                                // statepath looks like data.statements.record-{id}.name
                                $statement_id = collect(explode('.', $component->getStatePath()))
                                    ->filter(fn ($part) => str_starts_with($part, 'record-'))
                                    ->first();

                                $statement_id = Str::replace('record-', '', $statement_id);
                                $statement = Statement::find($statement_id);

                                if ($statement) {
                                    return $statement->policyDocuments->pluck('name')->toArray();
                                }

                                return [];

                            }),
                    )
                    ->addActionLabel('Add Statement')
                    ->deleteAction(fn (Action $action) => $action
                        ->tooltip('Delete Statement')
                        ->requiresConfirmation()
                        ->size('xs')
                        ->view(Action::LINK_VIEW)

                    )
                    ->extraItemActions([
                        Action::make('link-to-policies')

//                            ->hiddenLabel(false)
                            ->view(Action::LINK_VIEW)
                            ->size('xs')
                            ->label('Policy Documents')
                            ->icon('heroicon-o-link')
                            ->tooltip('+ Link to Policy Document(s)')
                            ->fillForm(function (array $arguments): array {
                                $statement_id = explode('-', $arguments['item'])[1];
                                $statement = Statement::find($statement_id);

                                if ($statement) {
                                    return [
                                        'policyDocuments' => $statement->policyDocuments>pluck('id')->toArray(),
                                    ];
                                }

                                return [];

                            })
                            ->schema([
                                Select::make('policyDocuments')
                                    ->multiple()
                                    ->options(PolicyDocument::where('assessment_id', Filament::getTenant()->id)->get()->pluck('name', 'id')->toArray())
                                    ->required(),
                            ])
                            ->action(function (array $arguments, array $data): void {

                                // check the statement exists (argument should be in the format record-354)
                                $statement_id = explode('-', $arguments['item'])[1];
                                $statement = Statement::find($statement_id);

                                if (! $statement) {
                                    // create the statement so we can link it to the policy
                                    $statement = Statement::create([
                                        'assessment_id' => Filament::getTenant()->id,
                                        'priority_action_id' => $this->priorityAction->id,
                                        'type_id' => $this->type->id,
                                        'name' => $this->data['statements'][$statement_id]['name'],
                                    ]);
                                }

                                $statement->policyDocuments()->sync($data['policyDocuments']);

                            }),
                    ]),
            ])
            ->statePath('data')
            ->model($this->priorityAction);
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
