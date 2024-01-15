<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Evidence extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

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
