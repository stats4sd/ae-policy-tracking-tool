<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Filament\Notifications\Notification;

class TestNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-notification';

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


        Notification::make('test')
            ->title('Testing')
            ->warning()
            ->body('This is a test notification.')
            ->broadcast(User::all())
            ->sendToDatabase(User::all())
            ->send();
    }
}
