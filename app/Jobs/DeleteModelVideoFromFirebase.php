<?php

namespace App\Jobs;

use App\Models\ModelVideo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteModelVideoFromFirebase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The video id instance.
     *
     * @var int
     */
    public $video_id;

    /**
     * Create a new job instance.
     *
     * @param  int  $video_id
     * @return void
     */
    public function __construct(int $video_id)
    {
        $this->video_id=$video_id;
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

        $db->collection('video')->document($this->video_id)->delete();
    }
}

