<?php

namespace App\Models;

use App\Models\Policy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PriorityAction extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    public function recommendation(): BelongsTo
    {
        return $this->belongsTo(Recommendation::class);
    }

    public function statements(): HasMany
    {
        return $this->hasMany(Statement::class);
    }

    public function highlights(): BelongsToMany
    {
        return $this->belongsToMany(Highlight::class);
    }
}
