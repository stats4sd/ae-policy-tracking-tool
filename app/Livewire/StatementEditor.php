<?php

namespace App\Livewire;

use App\Filament\Shared\Forms\Components\SimpleVisualRepeater;
use App\Models\AssessmentPriorityAction;
use App\Models\Type;
use Filament\Forms\Components\Repeater;
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
use Livewire\Component;

class StatementEditor extends Component implements HasForms
{
    use InteractsWithForms;

    public Type $type;
    public Collection $statements;
    public bool $first;
    public AssessmentPriorityAction $assessmentPriorityAction;

    ## Editing Stuff
    public ?array $data = [];
    public bool $editing = false;

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
                SimpleVisualRepeater::make('statements')
                    ->relationship(modifyQueryUsing: function (Builder $query) {
                        $query->where('type_id', $this->type->id);
                    })
                    ->hiddenLabel()
                    ->simple(
                        Textarea::make('name')
                            ->autosize()
                            ->required()
                            ->hiddenLabel(),
                    )
                    ->addActionLabel('Add Statement'),
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
