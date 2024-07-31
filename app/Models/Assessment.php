<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Policy;
use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


## Assessments are used as the tenant: users can join specific assessments, and the entire front-end is scoped to a specific assessment. Admin users should be able to access all assessments; other users may have access to one or multiple based on specific assignments.
class Assessment extends Model implements HasName
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

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function getFilamentName(): string
    {
        return $this->country->name;
    }
}
