<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Znck\Eloquent\Relations\BelongsToThrough;

class Extract extends Model
{
    use HasFactory, SoftDeletes;
    use \Znck\Eloquent\Traits\BelongsToThrough;

    protected function casts(): array
    {
        return [
            'verified' => 'boolean',
            'automatic' => 'boolean',
        ];
    }

    protected static function booted()
    {
        // Always order extracts by start_offset within a policy document
        static::addGlobalScope(function (Builder $query) {
            $query
                ->orderBy('extracts.policy_document_id', 'asc')
                ->orderBy('extracts.page_number', 'asc')
                ->orderBy('extracts.start_offset', 'asc');
        });
    }

    /** @return BelongsToThrough<Assessment, $this> */
    public function assessment(): BelongsToThrough
    {
        return $this->belongsToThrough(Assessment::class, [PolicyDocument::class]);
    }

    /** @return BelongsTo<PolicyDocument, $this> */
    public function policyDocument(): BelongsTo
    {
        return $this->belongsTo(PolicyDocument::class);
    }

    /** @return BelongsToMany<PriorityAction, $this> */
    public function priorityActions(): BelongsToMany
    {
        return $this->belongsToMany(PriorityAction::class);
    }

    /** @return BelongsToMany<SearchTerm, $this> */
    public function searchTerms(): BelongsToMany
    {
        return $this->belongsToMany(SearchTerm::class);
    }

    /** @return BelongsToMany<Statement, $this> */
    public function statements(): BelongsToMany
    {
        return $this->belongsToMany(Statement::class);
    }

    /** @return BelongsTo<Score, $this> */
    public function score(): BelongsTo
    {
        return $this->belongsTo(Score::class);
    }

    /** @return BelongsTo<Theme, $this> */
    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
    }

    public function formattedExtract(): Attribute
    {
        return new Attribute(
            get: function () {
                $extract = $this->extract;

                // Replace newlines with spaces
                $extract = str_replace("\n", ' ', $extract);
                $extract = str_replace("\r", ' ', $extract);

                // Replace multiple spaces with a single space
                $extract = preg_replace('/\s+/', ' ', $extract);

                return trim($extract);
            },
        );
    }
}
