<?php

namespace App\Filament\App\Resources\PolicyDocumentResource\Pages;

use App\Filament\App\Resources\PolicyDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPolicyDocuments extends ListRecords
{
    protected static string $resource = PolicyDocumentResource::class;

    protected ?string $heading = 'Policies Reviewed During this Assessment';
    protected ?string $subheading = 'During the assessment, this list should be updated with all the documents that have been reviewed. Individual statements should be linked to the relevant documents.';


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
