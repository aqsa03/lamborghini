<?php

namespace App\Observers;

use App\Models\Episode;
use App\Models\Season;

class EpisodeNextObserver
{
    /**
     * Handle the Episode "created" event.
     *
     * @param  \App\Models\Episode  $episode
     * @return void
     */
    public function created(Episode $episode)
    {
        foreach($episode->season->program->episodes() as $e){
            $e->next_episode_id = $e->findNextPublishedEpisode()->id ?? null;
            $e->prev_episode_id = $e->findPrevPublishedEpisode()->id ?? null;
            $e->save();
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
        if($episode->getOriginal('season_id') != $episode->season_id){
            foreach(Season::find($episode->getOriginal('season_id'))->program->episodes() as $e){
                $e->next_episode_id = $e->findNextPublishedEpisode()->id ?? null;
                $e->prev_episode_id = $e->findPrevPublishedEpisode()->id ?? null;
                $e->save();
            }    
        }
        foreach($episode->season->program->episodes() as $e){
            $e->next_episode_id = $e->findNextPublishedEpisode()->id ?? null;
            $e->prev_episode_id = $e->findPrevPublishedEpisode()->id ?? null;
            $e->save();
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
        if($episode?->season?->program){
            foreach($episode?->season?->program?->episodes() as $e){
                $e->next_episode_id = $e->findNextPublishedEpisode()->id ?? null;
                $e->prev_episode_id = $e->findPrevPublishedEpisode()->id ?? null;
                $e->save();
            }
        }
    }

    /**
     * Handle the Episode "restored" event.
     *
     * @param  \App\Models\Episode  $episode
     * @return void
     */
    public function restored(Episode $episode)
    {
        foreach($episode->season->program->episodes() as $e){
            $e->next_episode_id = $e->findNextPublishedEpisode()->id ?? null;
            $e->prev_episode_id = $e->findPrevPublishedEpisode()->id ?? null;
            $e->save();
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
        if($episode?->season?->program){
            foreach($episode?->season?->program?->episodes() as $e){
                $e->next_episode_id = $e->findNextPublishedEpisode()->id ?? null;
                $e->prev_episode_id = $e->findPrevPublishedEpisode()->id ?? null;
                $e->save();
            }
        }
    }
}
