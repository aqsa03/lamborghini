<?php

namespace App\Observers;

use App\Jobs\PushProgramToFirebase;
use App\Models\Category;

class CategoryTitleObserver
{
    /**
     * Handle the Program "updated" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function updated(Category $category)
    {
        if($category->getOriginal('title') != $category->title){
            foreach($category->programs as $p){
                PushProgramToFirebase::dispatch($p);       
            }
        }
    }
}
