<?php

namespace App\Filament\App\Resources\Extracts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ExtractForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('policy_document_id')
                    ->relationship('policyDocument', 'name')
                    ->required(),
                TextInput::make('start_offset')
                    ->required()
                    ->numeric(),
                TextInput::make('end_offset')
                    ->required()
                    ->numeric(),
                TextInput::make('color')
                    ->required(),
                Textarea::make('extract')
                    ->columnSpanFull(),
                Toggle::make('automatic')
                    ->required(),
                Toggle::make('verified')
                    ->required(),
            ]);
    }
}
