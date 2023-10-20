<?php

namespace App\Observers;

use App\Models\Program;
use App\Jobs\PushEpisodeToFirebase;
use App\Jobs\PushSeasonToFirebase;

class ProgramPodcastObserver
{
    /**
     * Handle the Program "updated" event.
     *
     * @param  \App\Models\Program  $program
     * @return void
     */
    public function updated(Program $program)
    {
        if($program->getOriginal('podcast') != $program->podcast){
            foreach($program->seasons as $s){
                PushSeasonToFirebase::dispatch($s);       
            }
            foreach($program->episodes() as $e){
                PushEpisodeToFirebase::dispatch($e);       
            }
        }
    }
}
