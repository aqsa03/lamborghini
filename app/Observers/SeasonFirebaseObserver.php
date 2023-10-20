<?php

namespace App\Observers;

use App\Models\Season;
use App\Jobs\PushSeasonToFirebase;
use App\Jobs\DeleteSeasonFromFirebase;
use App\Enums\SeasonStatus;
use Carbon\Carbon;

class SeasonFirebaseObserver
{
    /**
     * Handle the Season "created" event.
     *
     * @param  \App\Models\Season  $season
     * @return void
     */
    public function created(Season $season)
    {
        if(
            $season->status == SeasonStatus::PUBLISHED->value
            // and Carbon::parse($season->ordered_at) <= Carbon::now()
        ){
            PushSeasonToFirebase::dispatch($season);
        }
    }

    /**
     * Handle the Season "updated" event.
     *
     * @param  \App\Models\Season  $season
     * @return void
     */
    public function updated(Season $season)
    {
        if(
            $season->status == SeasonStatus::PUBLISHED->value 
            // and Carbon::parse($season->ordered_at) <= Carbon::now()
        ){
            PushSeasonToFirebase::dispatch($season);
        } else {
            if($season->published_at){
                DeleteSeasonFromFirebase::dispatch($season->id);
            }
        }
    }

    /**
     * Handle the Season "deleted" event.
     *
     * @param  \App\Models\Season  $season
     * @return void
     */
    public function deleted(Season $season)
    {
        DeleteSeasonFromFirebase::dispatch($season->id);
    }

    /**
     * Handle the Season "restored" event.
     *
     * @param  \App\Models\Season  $season
     * @return void
     */
    public function restored(Season $season)
    {
        if($season->status == SeasonStatus::PUBLISHED->value){
            PushSeasonToFirebase::dispatch($season);
        } else {
            if($season->published_at){
                DeleteSeasonFromFirebase::dispatch($season->id);
            }
        }
    }

    /**
     * Handle the Season "force deleted" event.
     *
     * @param  \App\Models\Season  $season
     * @return void
     */
    public function forceDeleted(Season $season)
    {
        DeleteSeasonFromFirebase::dispatch($season->id);
    }
}
