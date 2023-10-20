<?php

namespace App\Observers;

use App\Models\PageSection;
use App\Jobs\PushPageSectionToFirebase;
use App\Jobs\DeletePageSectionFromFirebase;

class PageSectionFirebaseObserver
{
    /**
     * Handle the PageSection "created" event.
     *
     * @param  \App\Models\PageSection  $pageSection
     * @return void
     */
    public function created(PageSection $pageSection)
    {
        PushPageSectionToFirebase::dispatch($pageSection);
    }

    /**
     * Handle the PageSection "updated" event.
     *
     * @param  \App\Models\PageSection  $pageSection
     * @return void
     */
    public function updated(PageSection $pageSection)
    {
        PushPageSectionToFirebase::dispatch($pageSection);
    }

    /**
     * Handle the PageSection "deleted" event.
     *
     * @param  \App\Models\PageSection  $pageSection
     * @return void
     */
    public function deleted(PageSection $pageSection)
    {
        DeletePageSectionFromFirebase::dispatch($pageSection->id, $pageSection->page_id);
    }

    /**
     * Handle the PageSection "restored" event.
     *
     * @param  \App\Models\PageSection  $pageSection
     * @return void
     */
    public function restored(PageSection $pageSection)
    {
        PushPageSectionToFirebase::dispatch($pageSection);
    }

    /**
     * Handle the PageSection "force deleted" event.
     *
     * @param  \App\Models\PageSection  $pageSection
     * @return void
     */
    public function forceDeleted(PageSection $pageSection)
    {
        DeletePageSectionFromFirebase::dispatch($pageSection->id, $pageSection->page_id);
    }
}
