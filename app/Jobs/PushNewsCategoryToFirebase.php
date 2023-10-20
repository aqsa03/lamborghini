<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\NewsCategory;

class PushNewsCategoryToFirebase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * The newsCategory instance.
     *
     * @var \App\Models\NewsCategory
     */
    public $newsCategory;

    /**
     * Create a new job instance.
     *
     * @param  App\Models\NewsCategory  $newsCategory
     * @return void
     */
    public function __construct(NewsCategory $newsCategory)
    {
        $this->newsCategory = $newsCategory;
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

        $db->collection('news_categories')->document($this->newsCategory->id)->set(
            ['title' => $this->newsCategory->title]
        );
    }
}
