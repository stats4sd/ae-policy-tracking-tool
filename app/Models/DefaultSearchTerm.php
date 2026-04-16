<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;
use Znck\Eloquent\Traits\BelongsToThrough;

class DefaultSearchTerm extends Model
{
    use BelongsToThrough;
    use HasFactory, HasTranslations;

    public array $translatable = ['phrase'];

    /** @return BelongsTo<PriorityAction, $this> */
    public function priorityAction(): BelongsTo
    {
        return $this->belongsTo(PriorityAction::class);
    }
}
