<?php

namespace App\Observers;

use App\Models\Video;
use App\Jobs\UploadVideoToMeride;
use App\Enums\VideoStatus;

class VideoObserver
{
    /**
     * Handle the Video "created" event.
     *
     * @param  \App\Models\Video  $video
     * @return void
     */
    public function created(Video $video)
    {
        if($video->meride_status == VideoStatus::SAVED->value){
            UploadVideoToMeride::dispatch($video);
        }
    }

    /**
     * Handle the Video "updated" event.
     *
     * @param  \App\Models\Video  $video
     * @return void
     */
    public function updated(Video $video)
    {
        if($video->meride_status == VideoStatus::SAVED->value){
            UploadVideoToMeride::dispatch($video);
        }

        if($video->meride_status == VideoStatus::READY->value){
            if($entity = $video->get_associated_entity()){
                if($entity->published_at && $entity->status == 'PUBLISHED'){
                    $class_name = '\\App\\Jobs\\Push'.substr(strrchr(get_class($entity), '\\'), 1).'ToFirebase';
                    call_user_func(array($class_name, 'dispatch'), $entity);
                }
            }
        }
    }

    /**
     * Handle the Video "deleted" event.
     *
     * @param  \App\Models\Video  $video
     * @return void
     */
    public function deleted(Video $video)
    {
        //
    }

    /**
     * Handle the Video "restored" event.
     *
     * @param  \App\Models\Video  $video
     * @return void
     */
    public function restored(Video $video)
    {
        //
    }

    /**
     * Handle the Video "force deleted" event.
     *
     * @param  \App\Models\Video  $video
     * @return void
     */
    public function forceDeleted(Video $video)
    {
        //
    }
}
