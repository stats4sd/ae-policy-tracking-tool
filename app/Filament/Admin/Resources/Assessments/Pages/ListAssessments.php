<?php

namespace App\Filament\Admin\Resources\Assessments\Pages;

use App\Filament\Admin\Resources\Assessments\AssessmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\RenderHook;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsRenderHook;

class ListAssessments extends ListRecords
{

protected Width|string|null $maxContentWidth = 'full';

protected static string $resource = AssessmentResource::class;

    protected static ?string $title = 'Assessments';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function getInfoPanel(): Section
    {
        return Section::make('Information')
            ->icon(Heroicon::InformationCircle)
            ->schema([
                Text::make('Each assessment represents a country-level agroecology policy evaluation. Assessments track progress through stages from In Progress to Finalised.'),
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
