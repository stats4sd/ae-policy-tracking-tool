<?php

namespace App\Listeners;

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
        if (! $policyDocument instanceof \App\Models\PolicyDocument) {
            return;
        }

        // Dispatch job to extract content
        \App\Jobs\ExtractPolicyDocumentContent::dispatch($policyDocument);
    }
}
