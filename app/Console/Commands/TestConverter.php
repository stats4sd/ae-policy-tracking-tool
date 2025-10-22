<?php

namespace App\Console\Commands;

use App\Models\PolicyDocument;
use Illuminate\Console\Command;
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

        // //        // Spatie PDF to Text package usage
        $text = (new Pdf)
            ->setPdf(base_path('tests/Livestock_Policy_2006.pdf'))
            ->addOptions([
                '-layout', // maintain original physical layout
                // '-htmlmeta', // include a simple HTML header with metadata
                '-nopgbrk', // do not insert page breaks between pages
            ])
            ->text();

        // save to file for reference
        file_put_contents(base_path('tests/Livestock_Policy_2006.txt'), $text);
        //
        //        $pandocIn = escapeshellarg(base_path('tests/Livestock_Policy_2006.txt'));
        //        $pandocOut = escapeshellarg(base_path('tests/Livestock_Policy_2006.html'));
        //
        //        $pandocCommand = 'pandoc -f markdown+hard_line_breaks -t html -s -o '.$pandocOut.' '.$pandocIn;
        //
        //        shell_exec($pandocCommand);
        //

        // save to db
        PolicyDocument::first()
            ->update(['content' => $text]);

    }
}
