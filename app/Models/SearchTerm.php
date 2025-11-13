<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SearchTerm extends Model
{

    /** @return BelongsTo<PriorityAction> */
    public function priorityAction(): BelongsTo
    {
        return $this->belongsTo(PriorityAction::class);
    }



}
