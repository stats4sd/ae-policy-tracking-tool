<?php

namespace App\Filament\Admin\Resources\Scores\Pages;

use App\Filament\Admin\Resources\Scores\ScoreResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\RenderHook;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsRenderHook;

class ListScores extends ListRecords
{
    protected static string $resource = ScoreResource::class;

    protected static ?string $title = 'Scoring';

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
                Text::make('Here, you can define the scoring scale used to rate policy alignment within assessments. Each extracted quote from policy documents is assigned one of these scores, which are then used to calculate overall scores for each CFS Policy Recommendation.'),
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
