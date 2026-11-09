<?php

namespace App\Filament\Admin\Pages;

use App\Support\Dashboard\MockDashboardData;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Support\Icons\Heroicon;
use Livewire\Attributes\Url;

class AdminDashboard extends Page
{
    protected static string $routePath = '/';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Cog6Tooth;

    protected static ?string $title = 'Tool Administration';

    protected string $view = 'filament.admin.pages.admin-dashboard';

    #[Url]
    public string $status = 'all';

    public static function getRoutePath(Panel $panel): string
    {
        return static::$routePath;
    }

    protected function getViewData(): array
    {
        $portfolio = collect(MockDashboardData::portfolio());

        $statusCounts = $portfolio->countBy('status');

        $visible = $this->status === 'all' ?
            $portfolio :
            $portfolio->where('status', $this->status);

        return [
            'portfolio' => $visible,
            'scores' => MockDashboardData::scores(),
            'signals' => MockDashboardData::portfolioSignals(),
            'statuses' => ['In Progress', 'Review', 'Finalised', 'Dropped'],
            'stats' => [
                'assessments' => $portfolio->count(),
                'countries' => $portfolio->pluck('country')->unique()->count(),
                'statusCounts' => $statusCounts,
                'documents' => $portfolio->sum('docs'),
                'verifiedPct' => round($portfolio->where('status', '!=', 'Dropped')->avg('verified_pct')),
            ],
            'progressSteps' => ['Documents', 'Search', 'Verify', 'Score', 'Statements', 'Key findings'],
        ];
    }
}
