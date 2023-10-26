<?php

namespace App\Observers;

use App\Enums\VideosStatus;
use App\Enums\VideoStatus;
use App\Models\ModelVideo;
use App\Jobs\PushModelVideoToFirebase;
use App\Jobs\DeleteModelVideoFromFirebase;

class ModelVideoFirebaseObserver
{
       /**
     * Handle the ModelVideo "created" event.
     *
     * @param  \App\Models\ModelVideo  $ModelVideo
     * @return void
     */
    public function created(ModelVideo $ModelVideo)
    {
        if(
            $ModelVideo->status == VideosStatus::PUBLISHED->value 
            // and Carbon::parse($ModelVideo->ordered_at) <= Carbon::now()
        ){
            PushModelVideoToFirebase::dispatch($ModelVideo);
        }
    }

    /**
     * Handle the ModelVideo "updated" event.
     *
     * @param  \App\Models\ModelVideo  $ModelVideo
     * @return void
     */
    public function updated(ModelVideo $ModelVideo)
    {
        if(
            $ModelVideo->status == VideosStatus::PUBLISHED->value 
            // and Carbon::parse($ModelVideo->ordered_at) <= Carbon::now()
        ){
            PushModelVideoToFirebase::dispatch($ModelVideo);
        } else {
            if($ModelVideo->published_at){
                DeleteModelVideoFromFirebase::dispatch($ModelVideo->id);
            }
        }
    }

    /**
     * Handle the ModelVideo "deleted" event.
     *
     * @param  \App\Models\ModelVideo  $ModelVideo
     * @return void
     */
    public function deleted(ModelVideo $ModelVideo)
    {
        DeleteModelVideoFromFirebase::dispatch($ModelVideo->id);
    }

    /**
     * Handle the ModelVideo "restored" event.
     *
     * @param  \App\Models\ModelVideo  $ModelVideo
     * @return void
     */
    public function restored(ModelVideo $ModelVideo)
    {
        if($ModelVideo->status == VideosStatus::PUBLISHED->value){
            PushModelVideoToFirebase::dispatch($ModelVideo);
        } else {
            if($ModelVideo->published_at){
                DeleteModelVideoFromFirebase::dispatch($ModelVideo->id);
            }
        }
        PushModelVideoToFirebase::dispatch($ModelVideo);
    }

    /**
     * Handle the ModelVideo "force deleted" event.
     *
     * @param  \App\Models\ModelVideo  $ModelVideo
     * @return void
     */
    public function forceDeleted(ModelVideo $ModelVideo)
    {
        DeleteModelVideoFromFirebase::dispatch($ModelVideo);
    }
}
