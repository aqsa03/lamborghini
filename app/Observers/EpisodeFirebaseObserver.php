<?php

namespace App\Observers;

use App\Models\Episode;
use App\Jobs\PushEpisodeToFirebase;
use App\Jobs\DeleteEpisodeFromFirebase;
use App\Enums\EpisodeStatus;
use Carbon\Carbon;

class EpisodeFirebaseObserver
{
    /**
     * Handle the Episode "created" event.
     *
     * @param  \App\Models\Episode  $episode
     * @return void
     */
    public function created(Episode $episode)
    {
        if(
            $episode->status == EpisodeStatus::PUBLISHED->value 
            // and Carbon::parse($episode->ordered_at) <= Carbon::now()
        ){
            PushEpisodeToFirebase::dispatch($episode);
        }
    }

    /**
     * Handle the Episode "updated" event.
     *
     * @param  \App\Models\Episode  $episode
     * @return void
     */
    public function updated(Episode $episode)
    {
        if(
            $episode->status == EpisodeStatus::PUBLISHED->value 
            // and Carbon::parse($episode->ordered_at) <= Carbon::now()
        ){
            PushEpisodeToFirebase::dispatch($episode);
        } else {
            if($episode->published_at){
                DeleteEpisodeFromFirebase::dispatch($episode->id);
            }
        }
    }

    /**
     * Handle the Episode "deleted" event.
     *
     * @param  \App\Models\Episode  $episode
     * @return void
     */
    public function deleted(Episode $episode)
    {
        DeleteEpisodeFromFirebase::dispatch($episode->id);
    }

    /**
     * Handle the Episode "restored" event.
     *
     * @param  \App\Models\Episode  $episode
     * @return void
     */
    public function restored(Episode $episode)
    {
        if($episode->status == EpisodeStatus::PUBLISHED->value){
            PushEpisodeToFirebase::dispatch($episode);
        } else {
            if($episode->published_at){
                DeleteEpisodeFromFirebase::dispatch($episode->id);
            }
        }
    }

    /**
     * Handle the Episode "force deleted" event.
     *
     * @param  \App\Models\Episode  $episode
     * @return void
     */
    public function forceDeleted(Episode $episode)
    {
        DeleteEpisodeFromFirebase::dispatch($episode->id);
    }
}
