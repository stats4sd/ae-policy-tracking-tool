<?php

namespace App\Filament\App\Resources\PolicyResource\Pages;

use App\Filament\App\Resources\PolicyResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePolicy extends CreateRecord
{
    protected static string $resource = PolicyResource::class;

    protected ?string $heading = 'Upload / Link to Policy Document(s)';
    protected ?string $subheading = 'Please upload the policy document(s) or provide the URL to the document(s) that are part of the assessment.';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

}
