<?php

namespace App\Filament\Admin\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Support\Icons\Heroicon;

class AdminDashboard extends Page
{
    protected static string $routePath = '/';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Cog6Tooth;

    protected static ?string $title = 'Tool Administration';

    public static function getRoutePath(Panel $panel): string
    {
        return static::$routePath;
    }

    protected string $view = 'filament.admin.pages.admin-dashboard';
}
