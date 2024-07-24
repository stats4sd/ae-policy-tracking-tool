<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Policy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Assessment extends Model
{
    protected static function booted()
    {
        static::creating(function ($query) {
            $query->status = 'In Progress';
        });

        static::created(function (self $assessment) {

            foreach (PriorityAction::all() as $priority_action) {
                AssessmentPriorityAction::create(['assessment_id' => $assessment->id, 'priority_action_id' => $priority_action->id]);
            }

        });
    }


    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function assessmentPriorityActions(): HasMany
    {
        return $this->HasMany(AssessmentPriorityAction::class);
    }

    public function policies(): HasMany
    {
        return $this->hasMany(Policy::class);
    }

}
