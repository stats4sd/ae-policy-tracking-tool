<?php

namespace App\Livewire;

use App\Models\Assessment;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Schemas\Components\Component;

class AssessmentSearchTermsManager extends Component
{

    public Assessment $record;


    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.assessment-search-terms-manager');
    }

    public function table(Table $table): Table
    {


        return $table
            ->relationship(fn() => $this->record->searchTerms()->with('priorityAction.recommendation'))
            ->columns([
                TextColumn::make('priorityAction.recommendation.short_title')->label('Recommendation'),
                TextColumn::make('priorityAction.code_and_name')->label('Priority Action'),
                TextColumn::make('phrase')->label('Search Term'),
        ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                CreateAction::make(),
            ]);

    }
}
