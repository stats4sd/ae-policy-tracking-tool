<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Assessment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'country_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function assessmentPriorityActions(): HasMany
    {
        return $this->HasMany(AssessmentPriorityAction::class);
    }

    public function statements(): HasManyThrough
    {
        return $this->hasManyThrough(Statement::class, AssessmentPriorityAction::class);
    }

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
}
