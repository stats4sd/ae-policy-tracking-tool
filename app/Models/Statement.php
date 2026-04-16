<?php

namespace App\Models;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class Statement extends Model
{
    use HasFactory, HasRelationships;

    protected static function booted()
    {
        // add global scope to only get statements from the current assessment

        static::addGlobalScope('assessment', function ($query) {
            if (Filament::hasTenancy() && Filament::getTenant()) {
                $query->where('assessment_id', Filament::getTenant()->getKey());
            }
        });
    }

    /** @return Attribute<Collection<PolicyDocument>, never> */
    public function linkedPolicyDocuments(): Attribute
    {
        return Attribute::make(
            get: function () {
                $directLinks = $this->policyDocuments;
                $extractLinks = PolicyDocument::whereHas('extracts', function ($query) {
                    $query->whereHas('statements', function ($query) {
                        $query->where('statements.id', $this->id);
                    });
                })->get();

                return $directLinks->merge($extractLinks)->unique('id');
            },
        );
    }

    public function priorityAction(): BelongsTo
    {
        return $this->belongsTo(PriorityAction::class);
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function aePrinciples(): BelongsToMany
    {
        return $this->belongsToMany(AePrinciple::class);
    }

    /* @return BelongsToMany<PolicyDocument, $this> */
    public function policyDocuments(): BelongsToMany
    {
        return $this->belongsToMany(PolicyDocument::class);
    }

    /** @return BelongsToMany<Extract, $this> */
    public function extracts(): BelongsToMany
    {
        return $this->belongsToMany(Extract::class);
    }

    /** @return BelongsTo<Theme, $this> */
    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
    }
}
