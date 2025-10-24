<?php

namespace App\Filament\App\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Support\Exceptions\Halt;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\App\Resources\PolicyDocumentResource;

class BulkUploadPage extends Page implements HasForms
{
    use InteractsWithForms;

    // declare array to store submitted form data
    public ?array $data = []; 

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-on-square-stack';

    protected static ?string $title = 'Bulk Upload';

    // define custom page blade view file location
    protected static string $view = 'filament.app.pages.bulk-upload-page';

    public function mount(): void 
    {
        $this->form->fill();
    }
 
    // define form components
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\SpatieMediaLibraryFileUpload::make('documents')
                    ->label('Upload Policy Document(s)')
                    ->hint('If you have the policy document(s), please upload them here.')
                    ->multiple()
                    ->reorderable()
                    ->preserveFilenames()
                    ->collection('policy-documents'),
        ])
        ->statePath('data');
    }

    // define actions
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('save'),
        ];
    }

    // define what to do when user clicked Save button
    public function save(): void
    {
        try {
            // cannot access the uploaded files
            // in this custom page, we did not bind file upload component to any Laravel model yet...
            // do we need to relate the uploaded files to a Laravel model for successful upload?
            // 
            // Question: how to access the uploaded file in the submitted form?
            $data = $this->form->getState();
            ray($data);

            // TODO: create one policy document model for each uploaded file
 
        } catch (Halt $exception) {
            return;
        }

        // TODO: redirect to policy documents list page
        // redirect(PolicyDocumentResource::getUrl('index'));

        // hardcode temporary for testing
        $numberOfFiles = 3;

        // show notification
        Notification::make() 
            ->success()
            ->title($numberOfFiles . ' file uploaded and 3 policy documents created')
            ->send(); 
    }
}
