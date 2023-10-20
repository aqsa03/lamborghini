<?php

namespace App\Jobs;

use App\Models\PalimpsestItem;
use Illuminate\Bus\Queueable;
use Google\Cloud\Core\Timestamp;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class PushPalimpsestItemToFirebase implements ShouldQueue
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
     * The palimpsestItem instance.
     *
     * @var \App\Models\PalimpsestItem
     */
    public $palimpsestItem;

    /**
     * Create a new job instance.
     *
     * @param  App\Models\PalimpsestItem  $palimpsestItem
     * @return void
     */
    public function __construct(PalimpsestItem $palimpsestItem)
    {
        $this->palimpsestItem = $palimpsestItem;
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

        if(!$this->palimpsestItem->live_id){
            throw new \Exception('unable to push palimpsest item without live id to firebase');
        }

        $data = [
            'title' => $this->palimpsestItem->title,
            'description' => $this->palimpsestItem->description,
            'start_at' => $this->palimpsestItem->start_at ? new Timestamp(new \DateTime($this->palimpsestItem->start_at)) : null,
            'end_at' => $this->palimpsestItem->end_at ? new Timestamp(new \DateTime($this->palimpsestItem->end_at)) : null,
            // 'live' => $this->palimpsestItem->live_id ? $db->collection('lives')->document($this->palimpsestItem->live->id) : null,
            // 'live_id' => $this->palimpsestItem->live_id ? $this->palimpsestItem->live->id : null,
            'program' => $this->palimpsestItem->program_id ? $db->collection('programs')->document($this->palimpsestItem->program->id) : null,
            'program_id' => $this->palimpsestItem->program_id ? $this->palimpsestItem->program->id : null,
            'image' => $this->palimpsestItem->image ? [
                'source' => [
                    'url' => $this->palimpsestItem->image->url
                ],
                'xs' => [
                    'url' => $this->palimpsestItem->image->url.(($rule = config('image.imageManagerResizingQueryString.xs', false)) ? '?'.$rule : '')
                ],
                'sm' => [
                    'url' => $this->palimpsestItem->image->url.(($rule = config('image.imageManagerResizingQueryString.sm', false)) ? '?'.$rule : '')
                ],
                'md' => [
                    'url' => $this->palimpsestItem->image->url.(($rule = config('image.imageManagerResizingQueryString.md', false)) ? '?'.$rule : '')
                ],
                'lg' => [
                    'url' => $this->palimpsestItem->image->url.(($rule = config('image.imageManagerResizingQueryString.lg', false)) ? '?'.$rule : '')
                ],
                'xl' => [
                    'url' => $this->palimpsestItem->image->url.(($rule = config('image.imageManagerResizingQueryString.xl', false)) ? '?'.$rule : '')
                ]
            ] : null,
            'sharing_link' => [
                'url' => null,
            ],
        ];

        $db->collection('lives')->document($this->palimpsestItem->live_id)->collection('palimpsest_items')->document($this->palimpsestItem->id)->set($data);

    }
}
