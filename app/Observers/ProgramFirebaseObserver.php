<?php

namespace App\Observers;

use App\Models\Program;
use App\Jobs\PushProgramToFirebase;
use App\Jobs\DeleteProgramFromFirebase;
use App\Enums\ProgramStatus;
use Carbon\Carbon;

class ProgramFirebaseObserver
{
    /**
     * Handle the Program "created" event.
     *
     * @param  \App\Models\Program  $program
     * @return void
     */
    public function created(Program $program)
    {
        if(
            $program->status == ProgramStatus::PUBLISHED->value 
            // and Carbon::parse($program->ordered_at) <= Carbon::now()
        ){
            PushProgramToFirebase::dispatch($program);
        }
    }

    /**
     * Handle the Program "updated" event.
     *
     * @param  \App\Models\Program  $program
     * @return void
     */
    public function updated(Program $program)
    {
        if(
            $program->status == ProgramStatus::PUBLISHED->value 
            // and Carbon::parse($program->ordered_at) <= Carbon::now()
        ){
            PushProgramToFirebase::dispatch($program);
        } else {
            if($program->published_at){
                DeleteProgramFromFirebase::dispatch($program->id);
            }
        }
    }

    /**
     * Handle the Program "deleted" event.
     *
     * @param  \App\Models\Program  $program
     * @return void
     */
    public function deleted(Program $program)
    {
        DeleteProgramFromFirebase::dispatch($program->id);
    }

    /**
     * Handle the Program "restored" event.
     *
     * @param  \App\Models\Program  $program
     * @return void
     */
    public function restored(Program $program)
    {
        if($program->status == ProgramStatus::PUBLISHED->value){
            PushProgramToFirebase::dispatch($program);
        } else {
            if($program->published_at){
                DeleteProgramFromFirebase::dispatch($program->id);
            }
        }
    }

    /**
     * Handle the Program "force deleted" event.
     *
     * @param  \App\Models\Program  $program
     * @return void
     */
    public function forceDeleted(Program $program)
    {
        DeleteProgramFromFirebase::dispatch($program->id);
    }
}
