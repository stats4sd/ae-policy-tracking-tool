<?php

namespace App\Models;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
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

    /* @return HasManyDeep<PolicyDocument, $this> */
    public function policyDocuments(): HasManyDeep
    {

        // TODO: Verify if this is correct
        return $this->hasManyDeep(
            PolicyDocument::class,
            ['highlight_statement', Highlight::class],
            [null, 'id', 'id'],
            [null, 'statement_id', 'policy_document_id']
        );
    }

    /** @return BelongsToMany<Highlight, $this> */
    public function highlights(): BelongsToMany
    {
        return $this->belongsToMany(Highlight::class);
    }

}
