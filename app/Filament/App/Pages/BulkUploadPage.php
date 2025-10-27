<?php

namespace App\Filament\App\Pages;

use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use App\Models\PolicyDocument;
use Filament\Facades\Filament;
use Filament\Support\Exceptions\Halt;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
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
                FileUpload::make('documents')
                    ->label('Upload Policy Document(s)')
                    ->hint('If you have the policy document(s), please upload them here.')
                    ->required()
                    ->multiple()
                    // restrict file types to pdf, MS Word, text file
                    ->acceptedFileTypes([
                        'application/pdf',
                        'application/x-pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'text/plain',
                        ]) 
                    ->preserveFilenames()
                    // when storedFiles(false) is called, the $this->form->getState() will return an array of TemporaryUploadedFile objects 
                    // instead of an array of paths to the stored files
                    ->storeFiles(false),
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
            // get submitted form
            $data = $this->form->getState();

            // get the uploaded files from submitted form
            $documents = $data['documents'];

            // create one policy document model for each uploaded file
            foreach ($documents as $document) {
                // create policy document model, set original file name
                $policyDocument = PolicyDocument::create([
                    'assessment_id' => Filament::getTenant()->id,
                    'name' => $document->getClientOriginalName(),
                ]);

                // add the uploaded file to policy document model
                $policyDocument->addMedia($document->getRealPath())->toMediaCollection('policy-documents');

                // save policy document model
                $policyDocument->save();

                // get all uploaded file of the saved policy document
                $medias = $policyDocument->getMedia('policy-documents');

                // set original file name to media record
                // only need to update the first item because one policy document has one uploaded file only
                $medias[0]->name = $document->getClientOriginalName();
                $medias[0]->file_name = $document->getClientOriginalName();
                $medias[0]->save();                
            }

            // redirect to policy documents list page
            redirect(PolicyDocumentResource::getUrl('index'));

            // hardcode temporary for testing
            $numberOfFiles = count($documents);

            // show notification
            Notification::make() 
                ->success()
                ->title($numberOfFiles . ' file uploaded and ' . $numberOfFiles . ' policy documents created')
                ->send(); 
 
        } catch (Halt $exception) {
            return;
        }

    }
}
