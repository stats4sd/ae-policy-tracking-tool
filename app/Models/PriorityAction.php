<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PriorityAction extends Model
{
    protected $keyType = 'string';

    public $incrementing = false;

    public function recommendation(): BelongsTo
    {
        return $this->belongsTo(Recommendation::class);
    }

    public function statements(): HasMany
    {
        return $this->hasMany(Statement::class);
    }

    public function highlights(): BelongsToMany
    {
        return $this->belongsToMany(Highlight::class);
    }

    /** @return HasMany<SearchTerm, $this> */
    public function searchTerms(): HasMany
    {
        return $this->hasMany(SearchTerm::class);
    }

    /** @return HasMany<Theme, $this> */
    public function themes(): HasMany
    {
        return $this->hasMany(Theme::class);
    }
}
