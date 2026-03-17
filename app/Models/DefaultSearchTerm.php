<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class DefaultSearchTerm extends Model
{
    use HasTranslations;
    use \Znck\Eloquent\Traits\BelongsToThrough;

    public array $translatable = ['phrase'];

    /** @return BelongsTo<PriorityAction, $this> */
    public function priorityAction(): BelongsTo
    {
        return $this->belongsTo(PriorityAction::class);
    }
}
