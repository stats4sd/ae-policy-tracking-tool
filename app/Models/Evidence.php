<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Evidence extends Model
{
    use HasFactory;

    protected $fillable = [
        'evidence',
        'statement_id',
        'official_source',
    ];
    
    protected $casts = [
        'official_source' => 'boolean',
    ];

    public function statement(): BelongsTo
    {
        return $this->belongsTo(Statement::class);
    }
    
}
