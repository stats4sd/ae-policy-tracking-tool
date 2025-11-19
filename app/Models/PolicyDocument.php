<?php

namespace App\Models;

use App\Jobs\PolicyDocumentExtractContent;
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

    protected $table = 'policy_documents';

    protected $casts = [
        'text_direction' => TextDirection::class,
    ];

    /** @return BelongsTo<Assessment, $this> */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    /** @return BelongsToMany<Statement, $this> */
    public function statements(): BelongsToMany
    {
        return $this->belongsToMany(Statement::class);
    }

    /** @return HasMany<Highlight, $this> */
    public function highlights(): HasMany
    {
        return $this->hasMany(Highlight::class);
    }

    /** @return HasMany<Highlight, $this> */
    public function automaticHighlights(): HasMany
    {
        return $this->hasMany(Highlight::class)
            ->where('automatic', true)
            ->where('verified', false);
    }

    /** @return HasMany<Highlight, $this> */
    public function verifiedHighlights(): HasMany
    {
        return $this->hasMany(Highlight::class)
            ->where('verified', true)
            ->orWhere('automatic', false);
    }
}
