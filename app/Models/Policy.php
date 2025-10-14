<?php

namespace App\Models;

use App\Models\Assessment;
use App\Enums\TextDirection;
use App\Models\PriorityAction;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use App\Models\AssessmentPriorityAction;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// TODO: App panel throws error for missing "teams" table. It is not accessible now. There is a Policy resource in app panel.
// Rename Policy model to PolicyDocument model after app panel is accessible. This is to make sure related program files are 
// updated altogether and they work properly after program change.
class Policy extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $casts = [
        'text_direction' => TextDirection::class,
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function statements(): BelongsToMany
    {
        return $this->belongsToMany(Statement::class);
    }

}
