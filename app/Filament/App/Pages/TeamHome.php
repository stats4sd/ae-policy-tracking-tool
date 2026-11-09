<?php

namespace App\Filament\App\Pages;

use App\Support\Dashboard\MockDashboardData;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Support\Icons\Heroicon;

class TeamHome extends Page
{
    protected string $view = 'filament.app.pages.team-home';

    protected static string $routePath = '/home';

    protected static ?string $navigationLabel = 'Home';

    protected static ?string $title = 'Team home';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::Home;

    public static function getRoutePath(Panel $panel): string
    {
        return static::$routePath;
    }

    protected function getViewData(): array
    {
        return [
            'assessment' => MockDashboardData::assessment(),
            'progress' => MockDashboardData::teamProgress(),
        ];
    }
}
