<?php

namespace App\Observers;

use App\Enums\VideosStatus;
use App\Enums\VideoStatus;
use App\Models\ModelVideo;
use App\Jobs\PushModelVideoToFirebase;
use App\Jobs\DeleteModelVideoFromFirebase;
use Illuminate\Support\Facades\Log;

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
            Log::info('Initiating a Firestore create job to store videos.');
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
            Log::info('Initiating a Firestore update job for video synchronization.');
            PushModelVideoToFirebase::dispatch($ModelVideo);
        } else {
            if($ModelVideo->published_at){
                Log::info('Since Video is not ready so initiating a Firestore delete job to remove video.');
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
        Log::info('Initiating a Firestore delete job to remove video.');
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
            Log::info('Initiating a Firestore restore job for video synchronization.');
            PushModelVideoToFirebase::dispatch($ModelVideo);
        } else {
            if($ModelVideo->published_at){
                Log::info('Since Video is not ready so initiating a Firestore delete job to remove video.');
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
        Log::info('Initiating a Firestore forcedelete job to remove video.');
        DeleteModelVideoFromFirebase::dispatch($ModelVideo);
    }
}
