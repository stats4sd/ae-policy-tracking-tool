<?php

namespace App\Models;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class Statement extends Model
{
    use HasRelationships;

    protected static function booted()
    {
        // add global scope to only get statements from the current assessment

        static::addGlobalScope('assessment', function ($query) {
            if (Filament::hasTenancy() && Filament::getTenant()) {
                $query->where('assessment_id', Filament::getTenant()->id);
            }
        });
    }

    /** @return Attribute<Collection<PolicyDocument>> */
    public function linkedPolicyDocuments(): Attribute
    {
        return Attribute::make(
            get: function () {
                $directLinks = $this->policyDocuments;
                $highlightLinks = PolicyDocument::whereHas('highlights', function ($query) {
                    $query->whereHas('statements', function ($query) {
                        $query->where('statements.id', $this->id);
                    });
                })->get();

                return $directLinks->merge($highlightLinks)->unique('id');
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

    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class);
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

    /** @return BelongsToMany<Highlight, $this> */
    public function highlights(): BelongsToMany
    {
        return $this->belongsToMany(Highlight::class);
    }

    /** @return BelongsTo<Theme, $this> */
    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
    }
}
