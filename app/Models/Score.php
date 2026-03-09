<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Score extends Model
{
    /** @return HasMany<Extract, $this> */
    public function extracts(): HasMany
    {
        return $this->hasMany(Extract::class);
    }

}
