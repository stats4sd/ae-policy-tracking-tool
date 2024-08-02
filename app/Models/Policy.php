<?php

namespace App\Models;

use App\Models\Assessment;
use App\Models\PriorityAction;
use App\Models\AssessmentPriorityAction;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Policy extends Model implements HasMedia
{
    use InteractsWithMedia;

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function assessmentPriorityActions(): BelongsToMany
    {
        return $this->belongsToMany(AssessmentPriorityAction::class, 'assessment_priority_action_policy', 'policy_id', 'assessment_priority_action_id')
            ->using(AssessmentPriorityAction::class);
    }

    public function ()
    {
        
    }

}
