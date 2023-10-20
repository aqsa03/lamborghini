<?php

namespace App\Observers;

use App\Models\News;
use App\Jobs\PushNewsToFirebase;
use App\Jobs\DeleteNewsFromFirebase;
use App\Enums\NewsStatus;
use Carbon\Carbon;

class NewsFirebaseObserver
{
    /**
     * Handle the News "created" event.
     *
     * @param  \App\Models\News  $news
     * @return void
     */
    public function created(News $news)
    {
        if(
            $news->status == NewsStatus::PUBLISHED->value 
            // and Carbon::parse($news->ordered_at) <= Carbon::now()
        ){
            PushNewsToFirebase::dispatch($news);
        }
    }

    /**
     * Handle the News "updated" event.
     *
     * @param  \App\Models\News  $news
     * @return void
     */
    public function updated(News $news)
    {
        if(
            $news->status == NewsStatus::PUBLISHED->value 
            // and Carbon::parse($news->ordered_at) <= Carbon::now()
        ){
            PushNewsToFirebase::dispatch($news);
        } else {
            if($news->published_at){
                DeleteNewsFromFirebase::dispatch($news->id);
            }
        }
    }

    /**
     * Handle the News "deleted" event.
     *
     * @param  \App\Models\News  $news
     * @return void
     */
    public function deleted(News $news)
    {
        DeleteNewsFromFirebase::dispatch($news->id);
    }

    /**
     * Handle the News "restored" event.
     *
     * @param  \App\Models\News  $news
     * @return void
     */
    public function restored(News $news)
    {
        if($news->status == NewsStatus::PUBLISHED->value){
            PushNewsToFirebase::dispatch($news);
        } else {
            if($news->published_at){
                DeleteNewsFromFirebase::dispatch($news->id);
            }
        }
    }

    /**
     * Handle the News "force deleted" event.
     *
     * @param  \App\Models\News  $news
     * @return void
     */
    public function forceDeleted(News $news)
    {
        DeleteNewsFromFirebase::dispatch($news->id);
    }
}
