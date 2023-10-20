<?php

namespace App\Observers;

use App\Models\Program;

class ProgramDeleteObserver
{
    /**
     * Handle the Program "deleting" event.
     *
     * @param  \App\Models\Program  $program
     * @return void
     */
    public function deleting(Program $program)
    {
        foreach($program->seasons as $s){
            $s->delete();
        }
    }
}
