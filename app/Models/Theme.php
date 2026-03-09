<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Theme extends Model
{
    /** @return BelongsTo<Assessment, $this> */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    /** @return BelongsTo<PriorityAction, $this> */
    public function priorityAction(): BelongsTo
    {
        return $this->belongsTo(PriorityAction::class);
    }

    /** @return HasMany<Extract, $this> */
    public function extracts(): HasMany
    {
        return $this->hasMany(Extract::class);
    }

    /** @return HasMany<Statement, $this> */
    public function statements(): HasMany
    {
        return $this->hasMany(Statement::class);
    }
}
