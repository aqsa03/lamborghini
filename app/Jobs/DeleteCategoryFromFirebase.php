<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Category;
use Illuminate\Support\Facades\Log;

class DeleteCategoryFromFirebase implements ShouldQueue
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
    public $category_id;

    /**
     * Create a new job instance.
     *
     * @param  int  $category_id
     * @return void
     */
    public function __construct(int $category_id)
    {
        $this->category_id = $category_id;
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
        Log::info('Category to remove from Firestore',$this->category_id);
        $db->collection('categories')->document($this->category_id)->delete();
        Log::info('Successfully removed Category from Firestore');

    }
}
