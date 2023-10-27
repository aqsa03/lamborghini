<?php

namespace App\Observers;

use App\Enums\ModelStatus;
use App\Models\CarModel;
use App\Jobs\PushcarModelToFirebase;
use App\Jobs\DeleteCarModelFromFirebase;

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
        if(
            $CarModel->status == ModelStatus::PUBLISHED->value 
            // and Carbon::parse($program->ordered_at) <= Carbon::now()
        ){
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
        ) { PushCarModelToFirebase::dispatch($CarModel);}
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
        DeleteCarModelFromFirebase::dispatch($CarModel);
    }
}
