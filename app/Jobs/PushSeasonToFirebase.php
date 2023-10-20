<?php

namespace App\Jobs;

use App\Models\Season;
use App\Enums\SeasonStatus;
use App\Enums\VideoStatus;
use Illuminate\Bus\Queueable;
use Google\Cloud\Core\Timestamp;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class PushSeasonToFirebase implements ShouldQueue
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
     * The season instance.
     *
     * @var \App\Models\Season
     */
    public $season;

    /**
     * Create a new job instance.
     *
     * @param  App\Models\Season  $season
     * @return void
     */
    public function __construct(Season $season)
    {
        $this->season = $season;
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

        if(
            $this->season->status != SeasonStatus::PUBLISHED->value or
            !$this->season->published_at
        ){
            throw new \Exception('unable to push unpublished season to firebase');
        }
        $data = [
            'title' => $this->season->title,
            'short_description' => $this->season->short_description,
            'description' => $this->season->descriptionToHtml(),
            'tags' => $this->season->tags,
            'podcast' => $this->season->program->podcast ? true : false,
            'order_number' => $this->season->order_number,
            'program' => $this->season->program_id ? $db->collection('programs')->document($this->season->program->id) : null,
            'program_id' => $this->season->program_id ? $this->season->program->id : null,
            'published_date' => $this->season->published_at ? new Timestamp(new \DateTime($this->season->published_at)) : null,
            'image' => $this->season->image ? [
                'source' => [
                    'url' => $this->season->image->url
                ],
                'xs' => [
                    'url' => $this->season->image->url.(($rule = config('image.imageManagerResizingQueryString.xs', false)) ? '?'.$rule : '')
                ],
                'sm' => [
                    'url' => $this->season->image->url.(($rule = config('image.imageManagerResizingQueryString.sm', false)) ? '?'.$rule : '')
                ],
                'md' => [
                    'url' => $this->season->image->url.(($rule = config('image.imageManagerResizingQueryString.md', false)) ? '?'.$rule : '')
                ],
                'lg' => [
                    'url' => $this->season->image->url.(($rule = config('image.imageManagerResizingQueryString.lg', false)) ? '?'.$rule : '')
                ],
                'xl' => [
                    'url' => $this->season->image->url.(($rule = config('image.imageManagerResizingQueryString.xl', false)) ? '?'.$rule : '')
                ]
            ] : null,
            'image_poster' => $this->season->imagePoster ? [
                'source' => [
                    'url' => $this->season->imagePoster->url
                ],
                'xs' => [
                    'url' => $this->season->imagePoster->url.(($rule = config('image.imagePosterManagerResizingQueryString.xs', false)) ? '?'.$rule : '')
                ],
                'sm' => [
                    'url' => $this->season->imagePoster->url.(($rule = config('image.imagePosterManagerResizingQueryString.sm', false)) ? '?'.$rule : '')
                ],
                'md' => [
                    'url' => $this->season->imagePoster->url.(($rule = config('image.imagePosterManagerResizingQueryString.md', false)) ? '?'.$rule : '')
                ],
                'lg' => [
                    'url' => $this->season->imagePoster->url.(($rule = config('image.imagePosterManagerResizingQueryString.lg', false)) ? '?'.$rule : '')
                ],
                'xl' => [
                    'url' => $this->season->imagePoster->url.(($rule = config('image.imagePosterManagerResizingQueryString.xl', false)) ? '?'.$rule : '')
                ]
            ] : null,
            'video_full' => ($this->season->video and $this->season->video->meride_status == VideoStatus::READY->value) ? [
                'url' => $this->season->video->url,
                'url_mp4' => $this->season->video->url_mp4,
                'width' => $this->season->video->source_width,
                'height' => $this->season->video->source_height,
                'public' => $this->season->video->public ? true : false,
                'podcast' => $this->season->video->podcast ? true : false,
                'meride_embed_id' => $this->season->video->meride_embed_id,
                'duration' => $this->season->video->duration,
            ] : null,
            'video_preview' => ($this->season->videoPreview and $this->season->videoPreview->meride_status == VideoStatus::READY->value) ? [
                'url' => $this->season->videoPreview->url,
                'url_mp4' => $this->season->videoPreview->url_mp4,
                'width' => $this->season->videoPreview->source_width,
                'height' => $this->season->videoPreview->source_height,
                'public' => $this->season->videoPreview->public ? true : false,
                'podcast' => $this->season->videoPreview->podcast ? true : false,
                'meride_embed_id' => $this->season->videoPreview->meride_embed_id,
                'duration' => $this->season->videoPreview->duration,
            ] : null,
            'sharing_link' => [
                'url' => config('public_site.baseUrl').'detail/'.$this->season->program->id.'?season_id='.$this->season->id,
            ],
        ];

        $db->collection('seasons')->document($this->season->id)->set($data);

    }
}
