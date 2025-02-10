<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Filament\Admin\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends \Stats4sd\FilamentTeamManagement\Filament\Admin\Resources\UserResource
{
    protected static ?string $model = User::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Email')
                    ->placeholder('Email')
                    ->email()
                    ->required(),

                Select::make('assessment')
                    ->label('Which assessment(s) should the user be a member of?')
                    ->exists('assessments', 'id')
                    ->relationship('teams', titleAttribute: 'name')
                    ->live()
                    ->multiple(),

                // invite to role
                CheckboxList::make('roles')
                    ->relationship('roles', titleAttribute: 'name')
                    ->label('Select the user role(s) to assign')
                    ->exists('roles', 'id')
                    ->live(),
            ]);

    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
        ];
    }
}
