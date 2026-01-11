<?php

namespace App\Filament\App\Resources\Highlights\Pages;

use App\Filament\App\Resources\Highlights\HighlightResource;
use App\Livewire\ThemesForHighlightsTable;
use App\Models\PriorityAction;
use App\Models\Theme;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\RenderHook;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;

class ListHighlights extends ListRecords
{
    protected static string $resource = HighlightResource::class;

    protected Width|string|null $maxContentWidth = 'full';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Export to Excel')
                ->color('warning')
                ->action(function () {
                    // Export logic here

                    Notification::make('temp')
                        ->title('Feature coming soon')
                        ->body('This export feature is coming soon')
                        ->warning()
                        ->send();
                }),
        ];
    }

    public function getTabs(): array
    {

        $tabs = [
            'all' => Tab::make(),
        ];

        foreach (PriorityAction::with('themes')->get() as $priorityAction) {
            $tabs[$priorityAction->id] = Tab::make()
                ->label($priorityAction->id)
                ->schema([
                    Livewire::make(ThemesForHighlightsTable::class, ['priorityAction' => $priorityAction])
                        ->key('themes-for-highlights-table-'.$priorityAction->id),
                ])
                ->modifyQueryUsing(fn ($query) => $query->whereHas('priorityActions', function ($q) use ($priorityAction) {
                    $q->where('priority_action_id', $priorityAction->id);
                }));
        }

        return $tabs;
    }

    public function updatedActiveTab(): void
    {
        parent::updatedActiveTab();
        $this->resetTable();
        $this->removeTableFilters();
        $this->js('window.location.reload()');
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getTabsContentComponent(),
                RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE),

                EmbeddedTable::make(),
                RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_AFTER),
            ]);
    }
}
