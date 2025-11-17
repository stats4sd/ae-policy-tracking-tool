<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Highlight extends Model
{

    protected static function booted()
    {
        // Always order highlights by start_offset within a policy document
        static::addGlobalScope(function(Builder $query) {
            $query
                ->orderBy('highlights.policy_document_id', 'asc')
                ->orderBy('highlights.start_offset', 'asc');
        });
    }

    public function policyDocument(): BelongsTo
    {
        return $this->belongs(PolicyDocument::class);
    }

    public function priorityActions(): BelongsToMany
    {
        return $this->belongsToMany(PriorityAction::class);
    }
}
