<?php

namespace App\Observers;

use App\Models\Season;
use App\Jobs\PushEpisodeToFirebase;
use App\Jobs\PushSeasonToFirebase;

class SeasonOrderNumberObserver
{
    /**
     * Handle the Season "updated" event.
     *
     * @param  \App\Models\Season  $season
     * @return void
     */
    public function updated(Season $season)
    {
        if($season->getOriginal('order_number') != $season->order_number){
            foreach($season->episodes as $e){
                PushEpisodeToFirebase::dispatch($e);       
            }
        }
    }
}
