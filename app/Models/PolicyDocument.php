<?php

namespace App\Models;

use App\Enums\TextDirection;
use App\Jobs\PolicyDocumentAutoSearch;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

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

    /** @return HasMany<Extract, $this> */
    public function extracts(): HasMany
    {
        return $this->hasMany(Extract::class);
    }

    /** @return HasMany<Extract, $this> */
    public function automaticExtracts(): HasMany
    {
        return $this->hasMany(Extract::class)
            ->where('automatic', true)
            ->where('verified', false);
    }

    /** @return HasMany<Extract, $this> */
    public function verifiedExtracts(): HasMany
    {
        return $this->hasMany(Extract::class)
            ->where('verified', true)
            ->orWhere('automatic', false);
    }

    public function processing(): void
    {
        //        $this->update([
        //            'processing' => true,
        //        ]);
    }

    public function stopProcessing()
    {
        //        $this->update([
        //            'processing' => false,
        //        ]);
    }

    public function runAutomaticSearch()
    {

        $this->processing();

        PolicyDocumentAutoSearch::dispatch($this);
    }

    /** @return Attribute<string, $this> */
    public function yearString(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->year.($this->end_year ? ' - '.$this->end_year : ''),
        );
    }

    // by default, the policy document is in the default language of the assessment, but this can be overridden, for example if most documents are in French and only a few in English.
    /** @return BelongsTo<Language, $this> */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
