<?php

namespace App\Observers;

use App\Jobs\PushNewsToFirebase;
use App\Models\NewsCategory;

class NewsCategoryTitleObserver
{
    /**
     * Handle the Program "updated" event.
     *
     * @param  \App\Models\NewsCategory  $newsCategory
     * @return void
     */
    public function updated(NewsCategory $newsCategory)
    {
        if($newsCategory->getOriginal('title') != $newsCategory->title){
            foreach($newsCategory->news as $n){
                PushNewsToFirebase::dispatch($n);       
            }
        }
    }
}
