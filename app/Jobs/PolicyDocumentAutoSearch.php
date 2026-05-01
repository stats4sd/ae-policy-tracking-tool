<?php

namespace App\Jobs;

use App\Models\PolicyDocument;
use App\Models\PriorityAction;
use Filament\Notifications\Notification;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;
use Throwable;

class PolicyDocumentAutoSearch implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly PolicyDocument $policyDocument) {}

    public function handle(): void
    {
        $policyDocument = $this->policyDocument;

        $jobs = PriorityAction::all()->map(
            fn (PriorityAction $priorityAction) => new PolicyDocumentAutoSearchForPriorityAction($policyDocument, $priorityAction)
        )->all();

        Bus::batch($jobs)
            ->then(function (Batch $batch) use ($policyDocument): void {
                Notification::make('Auto Search Completed')
                    ->body('Automatic search for priority actions completed for document: '.$policyDocument->name)
                    ->success()
                    ->send();
                $policyDocument->stopProcessing();
            })
            ->catch(function (Batch $batch, Throwable $e) use ($policyDocument): void {
                $policyDocument->stopProcessing();
            })
            ->dispatch();
    }
}
