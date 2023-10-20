<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\PageSection;

class DeletePageSectionFromFirebase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The pageSection id instance.
     *
     * @var int
     */
    public $pageSection_id;

    /**
     * The page id instance.
     *
     * @var int
     */
    public $page_id;

    /**
     * Create a new job instance.
     *
     * @param  int  $pageSection_id
     * @param  int  $page_id
     * @return void
     */
    public function __construct(int $pageSection_id, int $page_id)
    {
        $this->pageSection_id = $pageSection_id;
        $this->page_id = $page_id;
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

        $db->collection('pages')->document($this->page_id)->collection('sections')->document($this->pageSection_id)->delete();
    }
}
