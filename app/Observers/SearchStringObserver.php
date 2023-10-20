<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;

class SearchStringObserver
{
    /**
     * Handle the model "creating" event.
     *
     * @param  \App\Model  $model
     * @return void
     */
    public function creating(Model $model)
    {
        $model->search_string = $model->genSearchString();
    }

    /**
     * Handle the model "updating" event.
     *
     * @param  \App\Model  $model
     * @return void
     */
    public function updating(Model $model)
    {
        $model->search_string = $model->genSearchString();
    }

    /**
     * Handle the model "updated" event.
     *
     * @param  \App\Model  $model
     * @return void
     */
    public function updated(Model $model)
    {
        switch(get_class($model)){
            case "App\Models\Program":
                foreach($model->seasons as $s){
                    $s->search_string = $s->genSearchString();
                    $s->saveQuietly();
                    foreach($s->episodes as $e){
                        $e->search_string = $e->genSearchString();
                        $e->saveQuietly();
                    }    
                }
                break;
            case "App\Models\Season":
                foreach($model->episodes as $e){
                    $e->search_string = $e->genSearchString();
                    $e->saveQuietly();
                }
                break;
        }
    }
}
