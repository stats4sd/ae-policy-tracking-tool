<?php

namespace App\Filament\App\Clusters\Setup;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;

class SetupCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;
    protected static string | BackedEnum | null $activeNavigationIcon = Heroicon::Cog6Tooth;

    protected static ?string $navigationLabel = '0. Setup';

}
