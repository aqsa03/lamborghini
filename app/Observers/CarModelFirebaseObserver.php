<?php

namespace App\Observers;

use App\Enums\ModelStatus;
use App\Models\CarModel;
use App\Jobs\PushCarModelToFirebase;
use App\Jobs\DeleteCarModelFromFirebase;
use Illuminate\Support\Facades\Log;

class CarModelFirebaseObserver
{
       /**
     * Handle the CarModel "created" event.
     *
     * @param  \App\Models\CarModel  $CarModel
     * @return void
     */
    public function created(CarModel $CarModel)
    {
        if(
            $CarModel->status == ModelStatus::PUBLISHED->value 
            // and Carbon::parse($program->ordered_at) <= Carbon::now()
        ){
            Log::info('Model Video is ready.Initiating a Firestore create job to store model.');
        PushCarModelToFirebase::dispatch($CarModel);
   
         } }

    /**
     * Handle the CarModel "updated" event.
     *
     * @param  \App\Models\CarModel  $CarModel
     * @return void
     */
    public function updated(CarModel $CarModel)
    {
        Log::info('Model video is ready .Initiating a Firestore update job for Model synchronization.', ['status'=> $CarModel->status]);
        
        if(
            $CarModel->status == ModelStatus::PUBLISHED->value 
            // and Carbon::parse($program->ordered_at) <= Carbon::now()
        ){
            Log::info('Model video is ready .Initiating a Firestore update job for Model synchronization.');
        PushCarModelToFirebase::dispatch($CarModel);
        
        }else {
            // if($CarModel->published_at){
            //     DeleteCarModelFromFirebase::dispatch($CarModel->id);
            // }
        }
        // foreach($CarModel->published_posts as $post){
        //     PushPostToFirebase::dispatch($post);
        // }
    }

    /**
     * Handle the CarModel "deleted" event.
     *
     * @param  \App\Models\CarModel  $CarModel
     * @return void
     */
    public function deleted(CarModel $CarModel)
    {
        Log::info('Initiating a Firestore delete job to remove model.');
        DeleteCarModelFromFirebase::dispatch($CarModel->id);
    }

    /**
     * Handle the CarModel "restored" event.
     *
     * @param  \App\Models\CarModel  $CarModel
     * @return void
     */
    public function restored(CarModel $CarModel)
    {
        if(
            $CarModel->status == ModelStatus::PUBLISHED->value 
        ) { 
            Log::info('Initiating a Firestore restore job for model synchronization.');
            PushCarModelToFirebase::dispatch($CarModel);}
        else {
            // if($CarModel->published_at){
            //     DeleteCarModelFromFirebase::dispatch($CarModel->id);
            // }
        }
    }

    /**
     * Handle the CarModel "force deleted" event.
     *
     * @param  \App\Models\CarModel  $CarModel
     * @return void
     */
    public function forceDeleted(CarModel $CarModel)
    {
        Log::info('Initiating a Firestore forcedelete job to remove model.');
        DeleteCarModelFromFirebase::dispatch($CarModel);
    }
}
