<?php

namespace App\Filament\App\Resources\PolicyDocuments\Pages;

use App\Filament\App\Resources\PolicyDocuments\PolicyDocumentResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreatePolicyDocument extends CreateRecord
{
    protected static string $resource = PolicyDocumentResource::class;

    protected ?string $heading = 'Upload / Link to Policy Document(s)';

    protected ?string $subheading = 'Please upload the policy document(s) or provide the URL to the document(s) that are part of the assessment.';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['assessment_id'] = Filament::getTenant()->id;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
