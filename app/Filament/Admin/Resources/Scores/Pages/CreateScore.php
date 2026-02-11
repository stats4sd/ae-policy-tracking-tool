<?php

namespace App\Filament\Admin\Resources\Scores\Pages;

use App\Filament\Admin\Resources\Scores\ScoreResource;
use Filament\Resources\Pages\CreateRecord;

class CreateScore extends CreateRecord
{
    protected static string $resource = ScoreResource::class;
    protected static bool $canCreateAnother = false;
}
