<?php

namespace App\Models;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Statement extends Model
{

    protected static function booted()
    {
        // add global scope to only get statements from the current assessment

        static::addGlobalScope('assessment', function ($query) {
            if (Filament::hasTenancy() && Filament::getTenant()) {
                $query->where('assessment_id', Filament::getTenant()->id);
            }
        });

    }

    public function priorityAction(): BelongsTo
    {
        return $this->belongsTo(PriorityAction::class);
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class);
    }

    public function aePrinciples(): BelongsToMany
    {
        return $this->belongsToMany(AePrinciple::class);
    }

    public function policies(): BelongsToMany
    {
        return $this->belongsToMany(Policy::class);
    }
}
