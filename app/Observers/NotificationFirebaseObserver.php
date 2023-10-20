<?php

namespace App\Observers;

use App\Models\Notification;
use App\Enums\NotificationStatus;
use App\Jobs\PushNotificationToFirebase;
use Carbon\Carbon;


class NotificationFirebaseObserver
{
    /**
     * Handle the Notification "created" event.
     *
     * @param  \App\Models\Notification  $notification
     * @return void
     */
    public function created(Notification $notification)
    {
        if(
            $notification->status == NotificationStatus::READY->value and 
            Carbon::parse($notification->scheduled_at) <= Carbon::now()
        ){
            PushNotificationToFirebase::dispatch($notification);
        }
    }

    /**
     * Handle the Notification "updated" event.
     *
     * @param  \App\Models\Notification  $notification
     * @return void
     */
    public function updated(Notification $notification)
    {
        if(
            $notification->status != NotificationStatus::SENT->value and
            Carbon::parse($notification->scheduled_at) <= Carbon::now()
        ){
            PushNotificationToFirebase::dispatch($notification);
        }
    }

    /**
     * Handle the Notification "deleted" event.
     *
     * @param  \App\Models\Notification  $notification
     * @return void
     */
    public function deleted(Notification $notification)
    {
        //
    }

    /**
     * Handle the Notification "restored" event.
     *
     * @param  \App\Models\Notification  $notification
     * @return void
     */
    public function restored(Notification $notification)
    {
        //
    }

    /**
     * Handle the Notification "force deleted" event.
     *
     * @param  \App\Models\Notification  $notification
     * @return void
     */
    public function forceDeleted(Notification $notification)
    {
        //
    }
}
