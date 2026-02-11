<?php

namespace App\Filament\Admin\Resources\AePrinciples\Pages;

use App\Filament\Admin\Resources\AePrinciples\AePrincipleResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\RenderHook;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\HtmlString;

class ListAePrinciples extends ListRecords
{
    protected static string $resource = AePrincipleResource::class;

    protected static ?string $title = 'Agroecology Principles';

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function getInfoPanel(): Section
    {
        return Section::make('Information')
            ->icon(Heroicon::InformationCircle)
            ->schema([
                Text::make(new HtmlString('Lists the 13 Agroecology Principles as defined by the <a href="https://www.agroecologytpp.org/knowledge/13-hlpe-agroecology-principles-leaflet/">HLPE</a>.')),
                Text::make(new HtmlString('The principles here will eventually be linked to the recommendations and priority actions defined in the rest of the assessment tool. For now, this page is a placeholder.')),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getTabsContentComponent(),
                RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE),
                $this->getInfoPanel(),
                EmbeddedTable::make(),
                RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_AFTER),
            ]);
    }
}
