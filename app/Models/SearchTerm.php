<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Znck\Eloquent\Relations\BelongsToThrough;

class SearchTerm extends Model
{
    use \Znck\Eloquent\Traits\BelongsToThrough;

    /** @return BelongsTo<Assessment, $this> */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    /** @return BelongsTo<PriorityAction, $this> */
    public function priorityAction(): BelongsTo
    {
        return $this->belongsTo(PriorityAction::class);
    }

    /** @return BelongsToThrough<Recommendation, $this> */
    public function recommendation(): BelongsToThrough
    {
        return $this->belongsToThrough(
            Recommendation::class,
            PriorityAction::class
        );
    }


    /** @return BelongsToMany<Extract, $this> */
    public function extracts(): BelongsToMany
    {
        return $this->belongsToMany(Extract::class);
    }


}
