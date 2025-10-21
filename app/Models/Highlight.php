<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Highlight extends Model
{
    public function policyDocument(): BelongsTo
    {
        return $this->belongs(PolicyDocument::class);
    }

    public function priorityActions(): BelongsToMany
    {
        return $this->belongsToMany(PriorityAction::class);
    }
}
