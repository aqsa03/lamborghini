<?php

namespace App\Observers;

use App\Models\Season;
use App\Jobs\PushSeasonToFirebase;
use App\Jobs\DeleteSeasonFromFirebase;
use App\Enums\SeasonStatus;
use Carbon\Carbon;

class SeasonDeleteObserver
{
    /**
     * Handle the Season "deleting" event.
     *
     * @param  \App\Models\Season  $season
     * @return void
     */
    public function deleting(Season $season)
    {
        foreach($season->episodes as $e){
            $e->delete();
        }
    }
}
