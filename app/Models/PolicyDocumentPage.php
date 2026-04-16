<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PolicyDocumentPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'policy_document_id',
        'page_number',
        'content',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(function (Builder $query) {
            $query->orderBy('policy_document_pages.page_number', 'asc');
        });
    }

    /** @return BelongsTo<PolicyDocument, $this> */
    public function policyDocument(): BelongsTo
    {
        return $this->belongsTo(PolicyDocument::class);
    }
}
