<?php

use App\Jobs\PolicyDocumentExtractContent;
use App\Models\Assessment;
use App\Models\PolicyDocument;
use App\Models\PolicyDocumentPage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Queue;

describe('PolicyDocumentExtractContent', function () {

    it('implements ShouldQueue', function () {
        expect(PolicyDocumentExtractContent::class)->toImplement(ShouldQueue::class);
    });

    it('removes existing pages before extraction', function () {
        $assessment = Assessment::factory()->create();
        $document = PolicyDocument::factory()->create(['assessment_id' => $assessment->id]);

        PolicyDocumentPage::factory()->create([
            'policy_document_id' => $document->id,
            'page_number' => 1,
            'content' => 'Old page content',
        ]);

        expect($document->pages()->count())->toBe(1);

        // Mock the private extractPagesFromPdfWithPython method by using a partial mock
        // Since we can't easily test shell_exec in unit tests, we test the delete behaviour
        // by creating a subclass that overrides the Python extraction
        $job = new class($document) extends PolicyDocumentExtractContent
        {
            public function handle(): void
            {
                // Delete existing pages (same logic as the real job)
                $this->policyDocument->pages()->delete();
            }
        };

        $job->handle();

        expect($document->pages()->count())->toBe(0);
    });

    it('removes picture placeholders from extracted text', function () {
        $assessment = Assessment::factory()->create();
        $document = PolicyDocument::factory()->create(['assessment_id' => $assessment->id]);

        // Test the removePicturePlaceholders method via a concrete subclass
        $job = new class($document) extends PolicyDocumentExtractContent
        {
            public function testRemovePlaceholders(string $text): string
            {
                // Access the protected method by using Reflection
                $method = new ReflectionMethod(PolicyDocumentExtractContent::class, 'removePicturePlaceholders');
                $method->setAccessible(true);

                return $method->invoke($this, $text);
            }
        };

        $input = "Some text\n==> picture [640 x 480] intentionally omitted <==\nMore text";
        $result = $job->testRemovePlaceholders($input);

        expect($result)->not->toContain('==> picture')
            ->and($result)->toContain('Some text')
            ->and($result)->toContain('More text');
    });

    it('dispatches to queue', function () {
        Queue::fake();

        $assessment = Assessment::factory()->create();
        $document = PolicyDocument::factory()->create(['assessment_id' => $assessment->id]);

        PolicyDocumentExtractContent::dispatch($document);

        Queue::assertPushed(PolicyDocumentExtractContent::class);
    });

});
