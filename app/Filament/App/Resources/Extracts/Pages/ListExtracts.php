<?php

namespace App\Filament\App\Resources\Extracts\Pages;

use App\Filament\App\Resources\Extracts\ExtractResource;
use App\Livewire\ThemesForExtractsTable;
use App\Models\PriorityAction;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\RenderHook;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\View\PanelsRenderHook;
use Illuminate\Database\Eloquent\Builder;

class ListExtracts extends ListRecords
{
    protected static string $resource = ExtractResource::class;

    protected Width|string|null $maxContentWidth = 'full';

    public function mount(): void
    {
        parent::mount();

        if (! $this->activeTab) {
            $this->activeTab = PriorityAction::first()?->id;
        }
    }

    public function table(Table $table): Table
    {
        return $table->modifyQueryUsing(function (Builder $query): void {
            if ($this->activeTab) {
                $query->whereHas('priorityActions', fn ($q) => $q->where('priority_action_id', $this->activeTab));
            }
        });
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Export to Excel')
                ->color('warning')
                ->action(function () {
                    Notification::make('temp')
                        ->title('Feature coming soon')
                        ->body('This export feature is coming soon')
                        ->warning()
                        ->send();
                }),
        ];
    }

    public function getHeading(): string
    {
        $priorityAction = PriorityAction::find($this->activeTab);

        return $priorityAction
            ? $priorityAction->code_and_short_name
            : 'Extracts';
    }

    public function content(Schema $schema): Schema
    {
        $priorityAction = PriorityAction::find($this->activeTab);

        return $schema
            ->components([
                Section::make('How to use this page')
                    ->icon(Heroicon::OutlinedInformationCircle)
                    ->iconColor('info')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Text::make('On this page, you can see all extracts from the reviewed policy documents, grouped by Priority Action. Use the dropdown in the navigation bar to switch between different priority actions.'),
                        Text::make('The purpose of this page is to allow you to go through the extracts and identify common themes for each priority action. Above the extracts table is a themes table, showing any themes created for the current priority action so far.'),
                        Text::make('You can create a theme in 2 ways. First, click "New Theme" above the themes table. Then, you can assign extracts to the theme by clicking on "edit + verify" in the extracts table. Secondly, you can select any number of extracts that you think share a common theme, and then click the "Assign to theme" button, and create a new theme using the dialog box. In this case, the selected extracts are automatically linked to the new theme.'),
                        Text::make('You may also create "summary statements", by selecting one or more extracts, and clicking "Summarise". Summary statements are broader statements that may tie multiple extracts together, but are not as high-level as "themes". A summary statement can also be linked to a specific theme.'),
                    ]),
                ...($priorityAction ? [
                    Livewire::make(ThemesForExtractsTable::class, ['priorityAction' => $priorityAction])
                        ->key('themes-for-extracts-table-'.$priorityAction->id),
                ] : []),
                RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE),
                EmbeddedTable::make(),
                RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_AFTER),
            ]);
    }
}
