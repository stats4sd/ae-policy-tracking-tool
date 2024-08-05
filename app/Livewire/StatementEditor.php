<?php

namespace App\Livewire;

use App\Filament\Shared\Forms\Components\SimpleRepeaterWithTags;
use App\Filament\Shared\Forms\Components\SimpleVisualRepeater;
use App\Filament\Shared\Forms\Components\TextAreaWithTags;
use App\Models\AssessmentPriorityAction;
use App\Models\Policy;
use App\Models\Statement;
use App\Models\Type;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action as FormComponentAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Support\Contracts\TranslatableContentDriver;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Livewire\Component;

class StatementEditor extends Component implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    public Type $type;
    public Collection $statements;
    public bool $first;
    public AssessmentPriorityAction $assessmentPriorityAction;

    ## Editing Stuff
    public ?array $data = [];
    public bool $editing = true;

    public function mount(): void
    {
        $this->form->fill($this->assessmentPriorityAction->toArray());
    }

    public function render()
    {
        return view('livewire.statement-editor');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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
                        ->tags(function(TextAreaWithTags $component): array {

                            // extract statement ID from state-path
                            // statepath looks like data.statements.record-{id}.name
                            $statement_id = collect(explode('.', $component->getStatePath()))
                            ->filter(fn($part) => str_starts_with($part, 'record-'))
                            ->first();

                            $statement_id = Str::replace('record-', '', $statement_id);
                            $statement = Statement::find($statement_id);

                            if($statement)  {
                                return $statement->policies->pluck('name')->toArray();
                            }

                            return [];

                        }),
                    )
                    ->addActionLabel('Add Statement')
                    ->deleteAction(fn(FormComponentAction $action) =>
                        $action
                            ->tooltip('Delete Statement')
                            ->requiresConfirmation()
                            ->size('xs')
                            ->view(FormComponentAction::LINK_VIEW)

                    )
                    ->extraItemActions([
                        FormComponentAction::make('link-to-policies')

//                            ->hiddenLabel(false)
                            ->view(FormComponentAction::LINK_VIEW)
                            ->size('xs')
                            ->label('Policy Documents')
                            ->icon('heroicon-o-link')
                            ->tooltip('+ Link to Policy Document(s)')
                            ->fillForm(function (array $arguments) {
                                $statement_id = explode('-', $arguments['item'])[1];
                                $statement = Statement::find($statement_id);

                                ray($statement->policies->pluck('id')->toArray());

                                return [
                                    'policies' => $statement->policies->pluck( 'id')->toArray(),
                                ];

                            })
                            ->form([
                                Select::make('policies')
                                    ->multiple()
                                    ->options(Policy::where('assessment_id', $this->assessmentPriorityAction->assessment->id)->get()->pluck('name', 'id')->toArray())
                                    ->required(),
                            ])
                            ->action(function (array $arguments, array $data): void {


                                // check the statement exists (argument should be in the format record-354)
                                $statement_id = explode('-', $arguments['item'])[1];
                                $statement = Statement::find($statement_id);

                                if(!$statement) {
                                    // create the statement so we can link it to the policy
                                    $statement = Statement::create([
                                        'assessment_priority_action_id' => $this->assessmentPriorityAction->id,
                                        'type_id' => $this->type->id,
                                        'name' => $this->data['statements'][$statement_id]['name'],
                                    ]);
                                }

                                $statement->policies()->sync($data['policies']);

                            }),
                    ]),
            ])
            ->statePath('data')
            ->model($this->assessmentPriorityAction);
    }

    public function update(): void
    {
        // add type_id to the data before saving
        // using saveRelationships() only works if all the required fields are in the form. We cannot do this here because we want the "simple" visuals of only having a single input in the repeater. So we have to manually add the type_id to the data before saving.
        $statements = collect($this->data['statements'])->map(function ($statement) {

            // if the statement doesn't already exist; add the type_id and create the entry
            if (!isset($statement['id'])) {
                $statement['type_id'] = $this->type->id;

                $this->assessmentPriorityAction->statements()->create($statement);

                // remove the statement from the relationship list.
                return null;
            }

            return $statement;
        })
            ->filter(fn($statement) => $statement !== null)
            ->toArray();


        $this->data['statements'] = $statements;

        $this->form->saveRelationships();

        // somehow the statements are saved just by me getting state.

        $this->editing = false;
        $this->assessmentPriorityAction->refresh();
        $this->form->fill($this->assessmentPriorityAction->toArray());


    }

}
