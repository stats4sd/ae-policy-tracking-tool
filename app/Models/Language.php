<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Language extends Model
{
    use HasTranslations;

    public array $translatable = ['name'];

    protected $keyType = 'string';

    public $incrementing = false;

    /** @return HasMany<Assessment, $this> */
    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    /** @return HasMany<PolicyDocument, $this> */
    public function policyDocuments(): HasMany
    {
        return $this->hasMany(PolicyDocument::class);
    }
}
