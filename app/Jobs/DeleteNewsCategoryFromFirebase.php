<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\NewsCategory;

class DeleteNewsCategoryFromFirebase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The newsCategory id instance.
     *
     * @var int
     */
    public $newsCategory_id;

    /**
     * Create a new job instance.
     *
     * @param  int  $newsCategory_id
     * @return void
     */
    public function __construct(int $newsCategory_id)
    {
        $this->newsCategory_id = $newsCategory_id;
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

        $db->collection('news_categories')->document($this->newsCategory_id)->delete();
    }
}
