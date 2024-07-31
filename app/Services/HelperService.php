<?php

namespace App\Services;


use App\Models\Assessment;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;

class HelperService
{


    // Helper function wrapper for Filament::getTenant().
    // This function is *only* to let IDEs realise that Filament::getTenant() can return an Assessment model.
    public static function getCurrentTenant(): Assessment|Model|null
    {
        if(Filament::hasTenancy()) {
            return Filament::getTenant();
        }

        return null;
    }
}
