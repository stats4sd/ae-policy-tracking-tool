<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AssessmentPriorityActionType extends Pivot
{
    use HasFactory;

    public function assessmentPriorityAction(): BelongsTo
    {
        return $this->belongsTo(AssessmentPriorityAction::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class);
    }

    public function statements(): HasMany
    {
        return $this->HasMany(Statement::class);
    }

}
