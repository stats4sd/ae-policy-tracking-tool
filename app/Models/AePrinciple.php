<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AePrinciple extends Model
{
    use HasFactory;

    public function recommendations(): BelongsToMany
    {
        return $this->belongsToMany(Recommendation::class);
    }

    public function statements(): HasMany
    {
        return $this->hasMany(Statement::class);
    }
}
