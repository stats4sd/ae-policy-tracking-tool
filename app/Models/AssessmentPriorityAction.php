<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AssessmentPriorityAction extends Pivot
{
    use HasFactory;

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function priorityAction(): BelongsTo
    {
        return $this->belongsTo(PriorityAction::class);
    }

    public function assessmentPriorityActionTypes(): HasMany
    {
        return $this->HasMany(AssessmentPriorityActionType::class);
    }

}
