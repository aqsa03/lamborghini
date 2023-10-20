<?php

namespace App\Observers;

use App\Models\PalimpsestItem;
use App\Jobs\PushPalimpsestItemToFirebase;
use App\Jobs\DeletePalimpsestItemFromFirebase;

class PalimpsestItemFirebaseObserver
{
    /**
     * Handle the PalimpsestItem "created" event.
     *
     * @param  \App\Models\PalimpsestItem  $palimpsestItem
     * @return void
     */
    public function created(PalimpsestItem $palimpsestItem)
    {
        PushPalimpsestItemToFirebase::dispatch($palimpsestItem);
    }

    /**
     * Handle the PalimpsestItem "updated" event.
     *
     * @param  \App\Models\PalimpsestItem  $palimpsestItem
     * @return void
     */
    public function updated(PalimpsestItem $palimpsestItem)
    {
        PushPalimpsestItemToFirebase::dispatch($palimpsestItem);
    }

    /**
     * Handle the PalimpsestItem "deleted" event.
     *
     * @param  \App\Models\PalimpsestItem  $palimpsestItem
     * @return void
     */
    public function deleted(PalimpsestItem $palimpsestItem)
    {
        DeletePalimpsestItemFromFirebase::dispatch($palimpsestItem->id, $palimpsestItem->live_id);
    }

    /**
     * Handle the PalimpsestItem "restored" event.
     *
     * @param  \App\Models\PalimpsestItem  $palimpsestItem
     * @return void
     */
    public function restored(PalimpsestItem $palimpsestItem)
    {
        PushPalimpsestItemToFirebase::dispatch($palimpsestItem);
    }

    /**
     * Handle the PalimpsestItem "force deleted" event.
     *
     * @param  \App\Models\PalimpsestItem  $palimpsestItem
     * @return void
     */
    public function forceDeleted(PalimpsestItem $palimpsestItem)
    {
        DeletePalimpsestItemFromFirebase::dispatch($palimpsestItem->id, $palimpsestItem->live_id);
    }
}
