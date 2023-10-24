<?php

namespace App\Jobs;

use App\Models\CarModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteCarModelFromFirebase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The category id instance.
     *
     * @var int
     */
    public $model_id;

    /**
     * Create a new job instance.
     *
     * @param  int  $model_id
     * @return void
     */
    public function __construct(int $model_id)
    {
        $this->model_id=$model_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $firestore = app('firebase.firestore');
        $db = $firestore->database();

        $db->collection('model')->document($this->model_id)->delete();
    }
}

