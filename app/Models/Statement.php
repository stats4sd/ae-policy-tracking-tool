<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Statement extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type_id'
    ];

    public function assessmentPriorityAction(): BelongsTo
    {
        return $this->belongsTo(AssessmentPriorityAction::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class);
    }

    public function aePrinciples(): BelongsToMany
    {
        return $this->belongsToMany(AePrinciple::class);
    }

    public function evidence(): HasMany
    {
        return $this->HasMany(Evidence::class);
    }
}
