<?php

namespace App\Filament\App\Resources\PolicyDocuments\Pages;

use App\Filament\App\Resources\PolicyDocuments\PolicyDocumentResource;
use App\Models\PolicyDocument;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;

class ReviewPolicyDocument extends ViewRecord
{
    protected static string $resource = PolicyDocumentResource::class;

    protected string $view = 'filament.app.resources.policy-document-resource.pages.review-policy-document';

    protected static ?string $breadcrumb = 'Review';

    protected Width|string|null $maxContentWidth = 'full';

    public function getTitle(): string|Htmlable
    {
        /** @var PolicyDocument $record */
        $record = $this->getRecord();

        return $record->name;
    }
}
