<?php

namespace App\Observers;

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
        PushCarModelToFirebase::dispatch($CarModel);
    }

    /**
     * Handle the CarModel "updated" event.
     *
     * @param  \App\Models\CarModel  $CarModel
     * @return void
     */
    public function updated(CarModel $CarModel)
    {
        PushCarModelToFirebase::dispatch($CarModel);
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
        PushCarModelToFirebase::dispatch($CarModel);
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
