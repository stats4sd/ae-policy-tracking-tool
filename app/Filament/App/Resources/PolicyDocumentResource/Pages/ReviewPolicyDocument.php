<?php

namespace App\Filament\App\Resources\PolicyDocumentResource\Pages;

use App\Filament\App\Resources\PolicyDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ReviewPolicyDocument extends ViewRecord
{
    protected static string $resource = PolicyDocumentResource::class;

    protected static string $view = 'filament.app.resources.policy-document-resource.pages.review-policy-document';

}
