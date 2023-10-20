<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeletePalimpsestItemFromFirebase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The palimpsestItem id.
     *
     * @var int
     */
    public $palimpsestItem_id;

    /**
     * The palimpsestItem live_id.
     *
     * @var int
     */
    public $live_id;

    /**
     * Create a new job instance.
     *
     * @param  int  $palimpsestItem_id
     * @param  int  $live_id
     * @return void
     */
    public function __construct(int $palimpsestItem_id, int $live_id)
    {
        $this->palimpsestItem_id = $palimpsestItem_id;
        $this->live_id = $live_id;
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

        $db->collection('lives')->document($this->live_id)->collection('palimpsest_items')->document($this->palimpsestItem_id)->delete();
    }
}
