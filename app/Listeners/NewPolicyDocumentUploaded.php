<?php

namespace App\Listeners;

use App\Jobs\PolicyDocumentAutoSearch;
use App\Models\PolicyDocument;
use App\Jobs\PolicyDocumentExtractContent;
use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAddedEvent;

class NewPolicyDocumentUploaded
{
    /**
     * Handle the event.
     */
    public function handle(MediaHasBeenAddedEvent $event): void
    {

        // Spatie media library doesn't have returns statements for model relation property, so ignore error
        /** phpstan-ignore */
        $policyDocument = $event->media->model;

        // only operate on PolicyDocument models
        if (! $policyDocument instanceof PolicyDocument) {
            return;
        }

        // Dispatch job to extract content
        PolicyDocumentExtractContent::dispatch($policyDocument);
        PolicyDocumentAutoSearch::dispatch($policyDocument);
    }
}
