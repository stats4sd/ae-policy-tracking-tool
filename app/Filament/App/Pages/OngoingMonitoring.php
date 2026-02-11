<?php

namespace App\Filament\App\Pages;

use Filament\Pages\Page;

class OngoingMonitoring extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament.app.pages.ongoing-monitoring';
}
