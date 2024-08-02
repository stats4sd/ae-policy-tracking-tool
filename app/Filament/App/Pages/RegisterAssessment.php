<?php

namespace App\Filament\App\Pages;

use App\Models\Country;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Pages\Tenancy\RegisterTenant;
use Illuminate\Contracts\View\View;
use PHPUnit\Framework\Constraint\Count;

class RegisterAssessment extends RegisterTenant
{

    protected static string $view = 'filament.app.pages.register-assessment';

    public static function getLabel(): string
    {
        return 'Start New Assessment';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('country_id')
                    ->label('Select or Add Country')
                    ->relationship('country', 'name')
                    ->createOptionForm(function (Form $form, $get) {

                        return $form
                            ->schema([
                                TextInput::make('name')
                                    ->label('Country Name: ' . $get('hello'))
                                    ->placeholder('Enter the name of the country')
                                    ->required(),
                            ]);
                    })
                    ->createOptionUsing(function ($data) {
                        $country = Country::create(['name' => $data['name']]);

                        return $country->id;
                    }),
            ]);
    }
}
