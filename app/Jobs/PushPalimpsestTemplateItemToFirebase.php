<?php

namespace App\Jobs;

use App\Models\PalimpsestTemplateItem;
use Illuminate\Bus\Queueable;
use Google\Cloud\Core\Timestamp;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class PushPalimpsestTemplateItemToFirebase implements ShouldQueue
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
     * The palimpsestTemplateItem instance.
     *
     * @var \App\Models\PalimpsestTemplateItem
     */
    public $palimpsestTemplateItem;

    /**
     * Create a new job instance.
     *
     * @param  App\Models\PalimpsestTemplateItem  $palimpsestTemplateItem
     * @return void
     */
    public function __construct(PalimpsestTemplateItem $palimpsestTemplateItem)
    {
        $this->palimpsestTemplateItem = $palimpsestTemplateItem;
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

        if(!$this->palimpsestTemplateItem->live_id){
            throw new \Exception('unable to push palimpsest item without live id to firebase');
        }

        $data = [
            'title' => $this->palimpsestTemplateItem->title,
            'description' => $this->palimpsestTemplateItem->description,
            'start_at' => $this->palimpsestTemplateItem->start_at,
            'end_at' => $this->palimpsestTemplateItem->end_at,
            'program' => $this->palimpsestTemplateItem->program_id ? $db->collection('programs')->document($this->palimpsestTemplateItem->program->id) : null,
            'program_id' => $this->palimpsestTemplateItem->program_id ? $this->palimpsestTemplateItem->program->id : null,
            'image' => $this->palimpsestTemplateItem->image ? [
                'source' => [
                    'url' => $this->palimpsestTemplateItem->image->url
                ],
                'xs' => [
                    'url' => $this->palimpsestTemplateItem->image->url.(($rule = config('image.imageManagerResizingQueryString.xs', false)) ? '?'.$rule : '')
                ],
                'sm' => [
                    'url' => $this->palimpsestTemplateItem->image->url.(($rule = config('image.imageManagerResizingQueryString.sm', false)) ? '?'.$rule : '')
                ],
                'md' => [
                    'url' => $this->palimpsestTemplateItem->image->url.(($rule = config('image.imageManagerResizingQueryString.md', false)) ? '?'.$rule : '')
                ],
                'lg' => [
                    'url' => $this->palimpsestTemplateItem->image->url.(($rule = config('image.imageManagerResizingQueryString.lg', false)) ? '?'.$rule : '')
                ],
                'xl' => [
                    'url' => $this->palimpsestTemplateItem->image->url.(($rule = config('image.imageManagerResizingQueryString.xl', false)) ? '?'.$rule : '')
                ]
            ] : null,
            'sharing_link' => [
                'url' => null,
            ],
        ];

        $db->collection('lives')->document($this->palimpsestTemplateItem->live_id)->collection('palimpsest_template')->document($this->palimpsestTemplateItem->day)->collection('items')->document($this->palimpsestTemplateItem->id)->set($data);

    }
}
