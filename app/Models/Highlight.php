<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Znck\Eloquent\Relations\BelongsToThrough;

class Highlight extends Model
{
    use SoftDeletes;
    use \Znck\Eloquent\Traits\BelongsToThrough;

    protected static function booted()
    {
        // Always order highlights by start_offset within a policy document
        static::addGlobalScope(function (Builder $query) {
            $query
                ->orderBy('highlights.policy_document_id', 'asc')
                ->orderBy('highlights.start_offset', 'asc');
        });
    }

    /** @return BelongsToThrough<Assessment, $this> */
    public function assessment(): BelongsToThrough
    {
        return $this->belongsToThrough(Assessment::class, [PolicyDocument::class]);
    }

    /** @return BelongsTo<PolicyDocument, $this> */
    public function policyDocument(): BelongsTo
    {
        return $this->belongsTo(PolicyDocument::class);
    }

    /** @return BelongsToMany<PriorityAction, $this> */
    public function priorityActions(): BelongsToMany
    {
        return $this->belongsToMany(PriorityAction::class);
    }

    /** @return BelongsToMany<SearchTerm, $this> */
    public function searchTerms(): BelongsToMany
    {
        return $this->belongsToMany(SearchTerm::class);
    }


}
