<?php

namespace App\Jobs;

use App\Models\Live;
use App\Enums\LiveStatus;
use App\Enums\VideoStatus;
use Illuminate\Bus\Queueable;
use Google\Cloud\Core\Timestamp;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class PushLiveToFirebase implements ShouldQueue
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
     * The live instance.
     *
     * @var \App\Models\Live
     */
    public $live;

    /**
     * Create a new job instance.
     *
     * @param  App\Models\Live  $live
     * @return void
     */
    public function __construct(Live $live)
    {
        $this->live = $live;
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

        $data = [
            'title' => $this->live->title,
            'short_description' => $this->live->short_description,
            // 'description' => $this->live->descriptionToHtml(),
            'tags' => $this->live->tags,
            // 'podcast' => $this->live->podcast ? true : false,
            'meride_embed_id' => $this->live->meride_embed_id,
            'image' => $this->live->image ? [
                'source' => [
                    'url' => $this->live->image->url
                ],
                'xs' => [
                    'url' => $this->live->image->url.(($rule = config('image.imageManagerResizingQueryString.xs', false)) ? '?'.$rule : '')
                ],
                'sm' => [
                    'url' => $this->live->image->url.(($rule = config('image.imageManagerResizingQueryString.sm', false)) ? '?'.$rule : '')
                ],
                'md' => [
                    'url' => $this->live->image->url.(($rule = config('image.imageManagerResizingQueryString.md', false)) ? '?'.$rule : '')
                ],
                'lg' => [
                    'url' => $this->live->image->url.(($rule = config('image.imageManagerResizingQueryString.lg', false)) ? '?'.$rule : '')
                ],
                'xl' => [
                    'url' => $this->live->image->url.(($rule = config('image.imageManagerResizingQueryString.xl', false)) ? '?'.$rule : '')
                ]
            ] : null,
            'image_poster' => $this->live->imagePoster ? [
                'source' => [
                    'url' => $this->live->imagePoster->url
                ],
                'xs' => [
                    'url' => $this->live->imagePoster->url.(($rule = config('image.imagePosterManagerResizingQueryString.xs', false)) ? '?'.$rule : '')
                ],
                'sm' => [
                    'url' => $this->live->imagePoster->url.(($rule = config('image.imagePosterManagerResizingQueryString.sm', false)) ? '?'.$rule : '')
                ],
                'md' => [
                    'url' => $this->live->imagePoster->url.(($rule = config('image.imagePosterManagerResizingQueryString.md', false)) ? '?'.$rule : '')
                ],
                'lg' => [
                    'url' => $this->live->imagePoster->url.(($rule = config('image.imagePosterManagerResizingQueryString.lg', false)) ? '?'.$rule : '')
                ],
                'xl' => [
                    'url' => $this->live->imagePoster->url.(($rule = config('image.imagePosterManagerResizingQueryString.xl', false)) ? '?'.$rule : '')
                ]
            ] : null,
            'sharing_link' => [
                'url' => config('public_site.baseUrl').'live/'.($this->live->podcast ? 'radio' : 'tv'),
            ],
        ];

        $db->collection('lives')->document($this->live->id)->set($data);

    }
}
