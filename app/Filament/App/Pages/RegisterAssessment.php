<?php

namespace App\Filament\App\Pages;

use Filament\Schemas\Schema;
use App\Models\Assessment;
use App\Models\Country;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Pages\Tenancy\RegisterTenant;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\Constraint\Count;

class RegisterAssessment extends RegisterTenant
{

    //protected string $view = 'filament.app.pages.register-assessment';

    public static function getLabel(): string
    {
        return 'Start New Assessment';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('country_id')
                    ->label('Select or Add Country')
                    ->relationship('country', 'name')
                    ->createOptionForm(function (Schema $schema, $get) {

                        return $schema
                            ->components([
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


    protected function handleRegistration(array $data): Model
    {
        $assessment = Assessment::create($data);

        $assessment->users()->sync(auth()->user());

        return $assessment;
    }
}
