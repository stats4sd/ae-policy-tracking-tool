<?php

namespace App\Filament\App\Resources\PolicyDocumentResource\Pages;

use App\Filament\App\Resources\PolicyDocumentResource;
use App\Models\PolicyDocument;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ReviewPolicyDocument extends ViewRecord
{
    protected static string $resource = PolicyDocumentResource::class;

    protected static string $view = 'filament.app.resources.policy-document-resource.pages.review-policy-document';

    protected ?string $maxContentWidth = 'full';

    public function getTitle(): string|Htmlable
    {
        /** @var PolicyDocument $record */
        $record = $this->getRecord();

        return $record->name;
    }
}
