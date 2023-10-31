<?php

namespace App\Observers;

use App\Models\Category;
use App\Jobs\PushCategoryToFirebase;
use App\Jobs\DeleteCategoryFromFirebase;
use App\Jobs\PushPostToFirebase;
use Illuminate\Support\Facades\Log;

class CategoryFirebaseObserver
{
    /**
     * Handle the Category "created" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function created(Category $category)
    {
        Log::info('Initiating a Firestore create job to store category.');
        PushCategoryToFirebase::dispatch($category);
    }

    /**
     * Handle the Category "updated" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function updated(Category $category)
    {
        Log::info('Initiating a Firestore update job for category synchronization.');
        PushCategoryToFirebase::dispatch($category);
        // foreach($category->published_posts as $post){
        //     PushPostToFirebase::dispatch($post);
        // }
    }

    /**
     * Handle the Category "deleted" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function deleted(Category $category)
    {
        Log::info('Initiating a Firestore delete job to remove category.');
        DeleteCategoryFromFirebase::dispatch($category->id);
    }

    /**
     * Handle the Category "restored" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function restored(Category $category)
    {
        Log::info('Initiating a Firestore restore job for category synchronization.');
        PushCategoryToFirebase::dispatch($category);
    }

    /**
     * Handle the Category "force deleted" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function forceDeleted(Category $category)
    {
        Log::info('Initiating a Firestore forcedelete job to remove category.');
        DeleteCategoryFromFirebase::dispatch($category);
    }
}
