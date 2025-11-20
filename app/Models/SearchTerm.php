<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SearchTerm extends Model
{

    /** @return BelongsTo<PriorityAction, $this> */
    public function priorityAction(): BelongsTo
    {
        return $this->belongsTo(PriorityAction::class);
    }


    /** @return BelongsToMany<Highlight, $this> */
    public function highlights(): BelongsToMany
    {
        return $this->belongsToMany(Highlight::class);
    }


}
