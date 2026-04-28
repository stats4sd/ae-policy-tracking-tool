<?php

namespace App\Filament\App\Clusters\Setup\Resources\SearchTerms\Pages;

use App\Filament\App\Clusters\Setup\Resources\SearchTerms\SearchTermResource;
use App\Models\Recommendation;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListSearchTerms extends ListRecords
{
    protected static string $resource = SearchTermResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('All'),
        ];

        foreach (Recommendation::orderBy('code')->get() as $recommendation) {
            $tabs[$recommendation->code] = Tab::make($recommendation->code . ' - ' . $recommendation->short_title)
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas(
                    'priorityAction',
                    fn (Builder $q) => $q->where('recommendation_id', $recommendation->id)
                ));
        }

        return $tabs;
    }
}
