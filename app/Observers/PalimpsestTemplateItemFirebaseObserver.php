<?php

namespace App\Observers;

use App\Models\PalimpsestTemplateItem;
use App\Jobs\PushPalimpsestTemplateItemToFirebase;
use App\Jobs\DeletePalimpsestTemplateItemFromFirebase;

class PalimpsestTemplateItemFirebaseObserver
{
    /**
     * Handle the PalimpsestTemplateItem "created" event.
     *
     * @param  \App\Models\PalimpsestTemplateItem  $palimpsestTemplateItem
     * @return void
     */
    public function created(PalimpsestTemplateItem $palimpsestTemplateItem)
    {
        PushPalimpsestTemplateItemToFirebase::dispatch($palimpsestTemplateItem);
    }

    /**
     * Handle the PalimpsestTemplateItem "updated" event.
     *
     * @param  \App\Models\PalimpsestTemplateItem  $palimpsestTemplateItem
     * @return void
     */
    public function updated(PalimpsestTemplateItem $palimpsestTemplateItem)
    {
        PushPalimpsestTemplateItemToFirebase::dispatch($palimpsestTemplateItem);
    }

    /**
     * Handle the PalimpsestTemplateItem "deleted" event.
     *
     * @param  \App\Models\PalimpsestTemplateItem  $palimpsestTemplateItem
     * @return void
     */
    public function deleted(PalimpsestTemplateItem $palimpsestTemplateItem)
    {
        DeletePalimpsestTemplateItemFromFirebase::dispatch($palimpsestTemplateItem->id, $palimpsestTemplateItem->live_id, $palimpsestTemplateItem->day);
    }

    /**
     * Handle the PalimpsestTemplateItem "restored" event.
     *
     * @param  \App\Models\PalimpsestTemplateItem  $palimpsestTemplateItem
     * @return void
     */
    public function restored(PalimpsestTemplateItem $palimpsestTemplateItem)
    {
        PushPalimpsestTemplateItemToFirebase::dispatch($palimpsestTemplateItem);
    }

    /**
     * Handle the PalimpsestTemplateItem "force deleted" event.
     *
     * @param  \App\Models\PalimpsestTemplateItem  $palimpsestTemplateItem
     * @return void
     */
    public function forceDeleted(PalimpsestTemplateItem $palimpsestTemplateItem)
    {
        DeletePalimpsestTemplateItemFromFirebase::dispatch($palimpsestTemplateItem->id, $palimpsestTemplateItem->live_id, $palimpsestTemplateItem->day);
    }
}
