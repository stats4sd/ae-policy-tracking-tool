<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ExtractPolicyDocumentContent implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public readonly \App\Models\PolicyDocument $policyDocument)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // logic to extract content from the policy document file
        $media = $this->policyDocument->getFirstMedia('policy-documents');
        if ($media) {
            $filePath = $media->getPath();

            $text = (new \Spatie\PdfToText\Pdf)
                ->setPdf($filePath)
                ->addOptions([
                    '-layout', // maintain original physical layout
                    '-nopgbrk', // do not insert page breaks between pages
                ])
                ->text();

            // formatting adjustments
            // 1. convert multiple .s to fewer dots
            $text = preg_replace('/\.{10,}/', '…', $text);
            // 2. convert too many spaces
            $text = preg_replace('/ {80,}/', '    ', $text);

            // Save extracted text to a file for reference
            $outputPath = storage_path('app/temp/'.$this->policyDocument->id.'.txt');
            file_put_contents($outputPath, $text);

            // save extracted text to the policy document record
            $this->policyDocument->update([
                'content' => $text,
            ]);
        }
    }
}
