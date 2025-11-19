<?php

namespace App\Console\Commands;

use App\Models\Highlight;
use App\Models\PolicyDocument;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Spatie\PdfToText\Pdf;

class TestConverter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-converter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $policyDocument = PolicyDocument::find(1);

        \App\Jobs\PolicyDocumentAutoSearch::dispatchSync($policyDocument);

        $this->info('done');

    }
}
