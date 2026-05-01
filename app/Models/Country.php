<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;

    /** @return HasMany<Jurisdiction, $this> */
    public function jurisdictions(): HasMany
    {
        return $this->hasMany(Jurisdiction::class);
    }
}
