<?php

namespace App\Observers;

use App\Models\Episode;
use App\Jobs\PushEpisodeToFirebase;
use App\Jobs\DeleteEpisodeFromFirebase;
use App\Enums\EpisodeStatus;
use App\Enums\SeasonStatus;
use App\Enums\ProgramStatus;
use App\Models\Season;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EpisodeObserver
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
            $episode->status == EpisodeStatus::PUBLISHED->value and 
            $episode->published_at
        ){
            if($episode->season->status != SeasonStatus::PUBLISHED->value or !$episode->season->published_at){
                $episode->season->status = SeasonStatus::PUBLISHED->value;
                $episode->season->published_at = date('Y-m-d H:i:s');
                $episode->season->save();
            }
            if($episode->season->program->status != ProgramStatus::PUBLISHED->value or !$episode->season->program->published_at){
                $episode->season->program->status = ProgramStatus::PUBLISHED->value;
                $episode->season->program->published_at = date('Y-m-d H:i:s');
                $episode->season->program->save();
            }
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
            $episode->status == EpisodeStatus::PUBLISHED->value and 
            $episode->published_at
        ){
            if($episode->season->status != SeasonStatus::PUBLISHED->value or !$episode->season->published_at){
                $episode->season->status = SeasonStatus::PUBLISHED->value;
                $episode->season->published_at = date('Y-m-d H:i:s');
                $episode->season->save();
            }
            if($episode->season->program->status != ProgramStatus::PUBLISHED->value or !$episode->season->program->published_at){
                $episode->season->program->status = ProgramStatus::PUBLISHED->value;
                $episode->season->program->published_at = date('Y-m-d H:i:s');
                $episode->season->program->save();
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
        //check if season has others published episodes
        if(Episode::where('season_id', '=', $episode->season_id)->where('id', '<>', $episode->id)->where('status', '=', EpisodeStatus::PUBLISHED->value)->count() == 0){
            if($episode->season->status == SeasonStatus::PUBLISHED->value and $episode->season->published_at){
                $episode->season->status = SeasonStatus::DRAFT->value;
                $episode->season->save();
            }
        }

        //check if program has others published episodes
        if(Episode::whereIn('season_id', DB::table('seasons')->select('id')->where('program_id', $episode->season->program_id))->where('id', '<>', $episode->id)->where('status', '=', EpisodeStatus::PUBLISHED->value)->count() == 0){
            if($episode->season->program->status == SeasonStatus::PUBLISHED->value and $episode->season->program->published_at){
                $episode->season->program->status = SeasonStatus::DRAFT->value;
                $episode->season->program->save();
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
        if(
            $episode->status == EpisodeStatus::PUBLISHED->value and 
            $episode->published_at
        ){
            if($episode->season->status != SeasonStatus::PUBLISHED->value or !$episode->season->published_at){
                $episode->season->status = SeasonStatus::PUBLISHED->value;
                $episode->season->published_at = date('Y-m-d H:i:s');
                $episode->season->save();
            }
            if($episode->season->program->status != ProgramStatus::PUBLISHED->value or !$episode->season->program->published_at){
                $episode->season->program->status = ProgramStatus::PUBLISHED->value;
                $episode->season->program->published_at = date('Y-m-d H:i:s');
                $episode->season->program->save();
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
        //check if season has others published episodes
        if(Episode::where('season_id', '=', $episode->season_id)->where('id', '<>', $episode->id)->where('status', '=', EpisodeStatus::PUBLISHED->value)->count() == 0){
            if($episode->season->status == SeasonStatus::PUBLISHED->value and $episode->season->published_at){
                $episode->season->status = SeasonStatus::DRAFT->value;
                $episode->season->save();
            }
        }

        //check if program has others published episodes
        if(Episode::whereIn('season_id', DB::table('seasons')->select('id')->where('program_id', $episode->season->program_id))->where('id', '<>', $episode->id)->where('status', '=', EpisodeStatus::PUBLISHED->value)->count() == 0){
            if($episode->season->program->status == SeasonStatus::PUBLISHED->value and $episode->season->program->published_at){
                $episode->season->program->status = SeasonStatus::DRAFT->value;
                $episode->season->program->save();
            }
        }
    }
}
