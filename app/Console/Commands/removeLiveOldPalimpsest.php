<?php

namespace App\Console\Commands;

use App\Models\PalimpsestItem;
use Carbon\Carbon;
use Illuminate\Console\Command;

class removeLiveOldPalimpsest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'palimpsestTV:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove palimpsests items older than 3 weeks';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        foreach(PalimpsestItem::where('start_at', '<', Carbon::parse(now())->subWeeks(3)->format('Y-m-d 00:00:00'))->get() as $del){
            $del->delete();
        }
        $this->info("Clean old palimpsest items");
    }
}
