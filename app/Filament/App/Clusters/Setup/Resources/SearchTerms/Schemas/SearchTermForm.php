<?php

namespace App\Filament\App\Clusters\Setup\Resources\SearchTerms\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SearchTermForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Select::make('priority_action_id')
                    ->relationship('priorityAction', 'code_and_short_name')
                    ->required(),
                TextInput::make('phrase')
                    ->required(),
            ]);
    }
}
