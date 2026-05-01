<?php

namespace App\Filament\App\Resources\PolicyDocuments\Pages;

use App\Filament\App\Resources\PolicyDocuments\PolicyDocumentResource;
use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class ListPolicyDocuments extends ListRecords
{
    protected static string $resource = PolicyDocumentResource::class;

    protected ?string $heading = 'Policies Reviewed During this Assessment';

    public ?string $autoSearchRestartedAt = null;

    protected function getListeners(): array
    {
        $assessmentId = Filament::getTenant()?->id;

        return [
            "echo-private:assessment.{$assessmentId},PolicyDocumentProcessingStarted" => 'refreshTable',
            "echo-private:assessment.{$assessmentId},PolicyDocumentProcessingStopped" => 'refreshTable',
        ];
    }

    public function refreshTable(): void
    {
        $this->resetTable();
    }

    public function content(Schema $schema): Schema
    {
        $mainConent = parent::content($schema);

        return $schema
            ->components([
                Section::make('How to use this page')
                    ->schema([

                        Text::make("On this page, you can view all the policy documents that have been uploaded for this assessment. You can also add new documents, review existing ones, or re-run the automatic search process to find relevant extracts based on the search terms you've defined."),
                        Text::make(new HtmlString("<b>Search & Find Extracts:</b> Click the 'Search & Find Extracts' action to review the extracts that have been automatically identified in the document based on your search terms. This will take you to a page where you can verify the extracts, add comments, and link them to priority actions.<br><br><b>Re-run Auto Search:</b> If you've made changes to your search terms or want to refresh the extracts for a document, you can use the 'Re-run Auto Search' action. This will delete any existing, unverified extracts for the document and run the automatic search process again to find new extracts based on the current search terms. Please note that this action will not affect any extracts that have already been verified.")),
                    ]),
                ...$mainConent->getComponents(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('bulk-upload')
                ->label('Bulk Upload')
                ->url(PolicyDocumentResource::getUrl('bulk-upload'))
                ->icon('heroicon-o-arrow-up-on-square'),
            CreateAction::make()
                ->label('Add New')
                ->icon('heroicon-o-plus'),
        ];
    }
}
