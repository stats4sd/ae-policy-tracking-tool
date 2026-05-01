<?php

namespace App\Jobs;

use App\Models\PolicyDocument;
use App\Models\PolicyDocumentPage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

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
        $media = $this->policyDocument->getFirstMedia('policy-documents');
        if (! $media) {
            return;
        }

        $filePath = $media->getPath();
        $pagesJson = $this->extractPagesFromPdfWithPython($filePath);
        $pages = json_decode($pagesJson, true);

        if (! is_array($pages)) {
            return;
        }

        // Remove existing pages for re-extraction support
        $this->policyDocument->pages()->delete();

        foreach ($pages as $page) {
            PolicyDocumentPage::create([
                'policy_document_id' => $this->policyDocument->id,
                'page_number' => $page['page'],
                'content' => $page['text'],
            ]);
        }
    }

    private function extractPagesFromPdfWithPython(string $filePath): string
    {
        $pythonScript = base_path('scripts/extract_with_pymupdf.py');
        $command = "venv/bin/python3 {$pythonScript} --pages {$filePath}";
        $output = shell_exec($command);

        $output = $this->removePicturePlaceholders($output ?: '');
        $output = $this->removeExtractedPictureText($output ?: '');

        return $output;

    }

    private function removePicturePlaceholders(string $text): string
    {
        return preg_replace('/==> picture \[\d+ x \d+\] intentionally omitted <==/m', '', $text);
    }

    private function removeExtractedPictureText(string $text): string
    {
        return preg_replace('/----- Start of picture text -----.*?----- End of picture text -----/s', '', $text) ?? $text;
    }
}
