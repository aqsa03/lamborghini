<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class syncAllFirebaseServices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'firebase:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all firebase services (Firestore and Realtime DB)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Artisan::call('firestore:sync');
        echo Artisan::output();
        // Artisan::call('realtimeDB:sync');
        // echo Artisan::output();
    }
}
