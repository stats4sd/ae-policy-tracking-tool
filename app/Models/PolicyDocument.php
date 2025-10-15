<?php

namespace App\Models;

use App\Models\Assessment;
use App\Enums\TextDirection;
use App\Models\PriorityAction;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use App\Models\AssessmentPriorityAction;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PolicyDocument extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'policies';

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

    public function highlights(): HasMany
    {
        return $this->hasMany(Highlight::class);
    }
}
