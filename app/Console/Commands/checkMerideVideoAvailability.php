<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Video;
use App\Enums\VideoStatus;

class checkMerideVideoAvailability extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'video:check {numVideo=10}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Meride Video Availability';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Video::where('meride_status', '!=', VideoStatus::READY->value)
            ->latest()
            ->take($this->argument('numVideo'))
            ->get()
            ->each(function ($item, $key) {
                if($item->check_meride_availability()){
                    $this->info('Video '.$item->id.' now available!');
                }
            });
    }
}
