<?php

use App\Jobs\PolicyDocumentExtractContent;
use App\Listeners\NewPolicyDocumentUploaded;
use App\Models\Assessment;
use App\Models\PolicyDocument;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAddedEvent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

describe('NewPolicyDocumentUploaded listener', function () {

    it('dispatches PolicyDocumentExtractContent when media is added to a PolicyDocument', function () {
        Queue::fake();

        $assessment = Assessment::factory()->create();
        $document = PolicyDocument::factory()->create(['assessment_id' => $assessment->id]);

        $media = Mockery::mock(Media::class)->makePartial();
        $media->model_type = PolicyDocument::class;
        $media->model_id = $document->id;
        $media->shouldReceive('getAttribute')->with('model')->andReturn($document);

        $event = new MediaHasBeenAddedEvent($media);

        (new NewPolicyDocumentUploaded)->handle($event);

        Queue::assertPushed(PolicyDocumentExtractContent::class, function ($job) use ($document) {
            return $job->policyDocument->id === $document->id;
        });
    });

    it('does not dispatch when media is added to a non-PolicyDocument model', function () {
        Queue::fake();

        // Create a mock media item attached to a non-PolicyDocument model
        $media = Mockery::mock(Media::class)->makePartial();
        $media->shouldReceive('getAttribute')->with('model')->andReturn(new User);

        $event = new MediaHasBeenAddedEvent($media);

        (new NewPolicyDocumentUploaded)->handle($event);

        Queue::assertNotPushed(PolicyDocumentExtractContent::class);
    });

});
