<?php

namespace App\Observers;

use App\Models\NewsCategory;
use App\Jobs\PushNewsCategoryToFirebase;
use App\Jobs\DeleteNewsCategoryFromFirebase;

class NewsCategoryFirebaseObserver
{
    /**
     * Handle the NewsCategory "created" event.
     *
     * @param  \App\Models\NewsCategory  $newsCategory
     * @return void
     */
    public function created(NewsCategory $newsCategory)
    {
        PushNewsCategoryToFirebase::dispatch($newsCategory);
    }

    /**
     * Handle the NewsCategory "updated" event.
     *
     * @param  \App\Models\NewsCategory  $newsCategory
     * @return void
     */
    public function updated(NewsCategory $newsCategory)
    {
        PushNewsCategoryToFirebase::dispatch($newsCategory);
        // foreach($newsCategory->published_posts as $post){
        //     PushPostToFirebase::dispatch($post);
        // }
    }

    /**
     * Handle the NewsCategory "deleted" event.
     *
     * @param  \App\Models\NewsCategory  $newsCategory
     * @return void
     */
    public function deleted(NewsCategory $newsCategory)
    {
        DeleteNewsCategoryFromFirebase::dispatch($newsCategory->id);
    }

    /**
     * Handle the NewsCategory "restored" event.
     *
     * @param  \App\Models\NewsCategory  $newsCategory
     * @return void
     */
    public function restored(NewsCategory $newsCategory)
    {
        PushNewsCategoryToFirebase::dispatch($newsCategory);
    }

    /**
     * Handle the NewsCategory "force deleted" event.
     *
     * @param  \App\Models\NewsCategory  $newsCategory
     * @return void
     */
    public function forceDeleted(NewsCategory $newsCategory)
    {
        DeleteNewsCategoryFromFirebase::dispatch($newsCategory);
    }
}
