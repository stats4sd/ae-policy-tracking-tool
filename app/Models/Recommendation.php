<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recommendation extends Model
{
    use HasFactory;

    public function aePrinciples(): BelongsToMany
    {
        return $this->belongsToMany(AePrinciple::class);
    }

    public function priorityActions(): HasMany
    {
        return $this->hasMany(PriorityAction::class);
    }
}
