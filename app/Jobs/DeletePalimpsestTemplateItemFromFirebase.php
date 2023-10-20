<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeletePalimpsestTemplateItemFromFirebase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The palimpsestTemplateItem id.
     *
     * @var int
     */
    public $palimpsestTemplateItem_id;

    /**
     * The palimpsestTemplateItem live_id.
     *
     * @var int
     */
    public $live_id;

    /**
     * The palimpsestTemplateItem day.
     *
     * @var int
     */
    public $day;

    /**
     * Create a new job instance.
     *
     * @param  int  $palimpsestTemplateItem_id
     * @param  int  $live_id
     * @param  string  $day
     * @return void
     */
    public function __construct(int $palimpsestTemplateItem_id, int $live_id, string $day)
    {
        $this->palimpsestTemplateItem_id = $palimpsestTemplateItem_id;
        $this->live_id = $live_id;
        $this->day = $day;
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

        $db->collection('lives')->document($this->live_id)->collection('palimpsest_template')->document($this->day)->collection('items')->document($this->palimpsestTemplateItem_id)->delete();
    }
}
