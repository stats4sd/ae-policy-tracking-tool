<?php

namespace App\Jobs;

use App\Models\PolicyDocument;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Psy\Util\Str;
use Spatie\PdfToText\Pdf;

class PolicyDocumentExtractContent implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public readonly PolicyDocument $policyDocument)
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


            $text = $this->extractTextFromPdfWithPython($filePath);

            // Save extracted text to a file for reference
            $outputPath = storage_path('app/temp/'.$this->policyDocument->id.'.txt');
            file_put_contents($outputPath, $text);

            // save extracted text to the policy document record
            $this->policyDocument->update([
                'content' => $text,
            ]);
        }
    }

    private function extractTextFromPdfWithPython(string $filePath): string
    {
        $pythonScript = base_path('scripts/extract_with_pymupdf.py');
        $command = "venv/bin/python3 {$pythonScript} {$filePath}";
        $output = shell_exec($command);

        return $output ?: '';
    }

    // Use the spatie/pdf-to-text package to extract text from the PDF, with some custom options and formatting adjustments.
    // Depreciated
    private function extractTextFromPdf(string $filePath): string
    {

        $text = (new Pdf)
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

        return $text;
    }

}
