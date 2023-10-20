<?php

namespace App\Observers;

use App\Models\Live;
use App\Jobs\PushLiveToFirebase;
use App\Jobs\DeleteLiveFromFirebase;

class LiveFirebaseObserver
{
    /**
     * Handle the Live "created" event.
     *
     * @param  \App\Models\Live  $live
     * @return void
     */
    public function created(Live $live)
    {
        PushLiveToFirebase::dispatch($live);
    }

    /**
     * Handle the Live "updated" event.
     *
     * @param  \App\Models\Live  $live
     * @return void
     */
    public function updated(Live $live)
    {
        PushLiveToFirebase::dispatch($live);
    }

    /**
     * Handle the Live "deleted" event.
     *
     * @param  \App\Models\Live  $live
     * @return void
     */
    public function deleted(Live $live)
    {
        DeleteLiveFromFirebase::dispatch($live->id);
    }

    /**
     * Handle the Live "restored" event.
     *
     * @param  \App\Models\Live  $live
     * @return void
     */
    public function restored(Live $live)
    {
        PushLiveToFirebase::dispatch($live);
    }

    /**
     * Handle the Live "force deleted" event.
     *
     * @param  \App\Models\Live  $live
     * @return void
     */
    public function forceDeleted(Live $live)
    {
        DeleteLiveFromFirebase::dispatch($live->id);
    }
}
