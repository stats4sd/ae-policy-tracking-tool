<?php

namespace App\Filament\App\Resources\Highlights\Pages;

use App\Filament\App\Resources\Highlights\HighlightResource;
use App\Models\PriorityAction;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\RenderHook;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Text;
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
        ];
    }

    public function getTabs(): array
    {


        $tabs = [
            'all' => Tab::make(),
        ];

        foreach(PriorityAction::all() as $priorityAction) {
            $tabs['priority-action-' . $priorityAction->id] = Tab::make()
                ->label($priorityAction->id)
                ->modifyQueryUsing(fn($query) => $query->whereHas('priorityActions', function ($q) use ($priorityAction) {
                    $q->where('priority_action_id', $priorityAction->id);
                }));
        }

        return $tabs;
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
