<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AssessmentPriorityAction extends Pivot
{

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function priorityAction(): BelongsTo
    {
        return $this->belongsTo(PriorityAction::class, 'priority_action_id');
    }

    public function statements(): HasMany
    {
        return $this->HasMany(Statement::class, 'assessment_priority_action_id');
    }

    public function policies(): BelongsToMany
    {
        return $this->belongsToMany(Policy::class, 'assessment_priority_action_policy', 'assessment_priority_action_id', 'policy_id')
            ->using(self::class);
    }
}
