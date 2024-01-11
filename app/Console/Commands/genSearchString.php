<?php

namespace App\Console\Commands;

use App\Models\Live;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class genSearchString extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'searchString:gen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate search string for programs, seasons, episodes, lives, news';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
       
        $this->info("Generated search string for collection Episode");

        Live::each(function ($entity) {
            $entity->search_string = $entity->genSearchString();
            $entity->saveQuietly();
        });
        $this->info("Generated search string for collection Live");

       
        $this->info("Generated search string for collection News");
    }
}
