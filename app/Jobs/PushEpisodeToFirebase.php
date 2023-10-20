<?php

namespace App\Jobs;

use App\Models\Episode;
use App\Enums\EpisodeStatus;
use App\Enums\VideoStatus;
use Illuminate\Bus\Queueable;
use Google\Cloud\Core\Timestamp;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class PushEpisodeToFirebase implements ShouldQueue
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
     * The episode instance.
     *
     * @var \App\Models\Episode
     */
    public $episode;

    /**
     * Create a new job instance.
     *
     * @param  App\Models\Episode  $episode
     * @return void
     */
    public function __construct(Episode $episode)
    {
        $this->episode = $episode;
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
            $this->episode->status != EpisodeStatus::PUBLISHED->value or
            !$this->episode->published_at
        ){
            throw new \Exception('unable to push unpublished episode to firebase');
        }
        $data = [
            'title' => $this->episode->title,
            'short_description' => $this->episode->short_description,
            'description' => $this->episode->descriptionToHtml(),
            'tags' => $this->episode->tags,
            'podcast' => $this->episode->season->program->podcast ? true : false,
            'order_number' => $this->episode->order_number,
            'season_number' => $this->episode->season->order_number,
            'program' => $this->episode->season->program_id ? $db->collection('programs')->document($this->episode->season->program->id) : null,
            'program_id' => $this->episode->season->program_id ? $this->episode->season->program->id : null,
            'season' => $this->episode->season_id ? $db->collection('seasons')->document($this->episode->season->id) : null,
            'season_id' => $this->episode->season_id ? $this->episode->season->id : null,
            'next_episode' => $this->episode->next_episode?->status == EpisodeStatus::PUBLISHED->value ? $db->collection('episodes')->document($this->episode->next_episode?->id) : null,
            'next_episode_id' => $this->episode->next_episode?->status == EpisodeStatus::PUBLISHED->value ? $this->episode->next_episode?->id : null,
            'prev_episode' => $this->episode->prev_episode?->status == EpisodeStatus::PUBLISHED->value ? $db->collection('episodes')->document($this->episode->prev_episode?->id) : null,
            'prev_episode_id' => $this->episode->prev_episode?->status == EpisodeStatus::PUBLISHED->value ? $this->episode->prev_episode?->id : null,
            'published_date' => $this->episode->published_at ? new Timestamp(new \DateTime($this->episode->published_at)) : null,
            'image' => $this->episode->image ? [
                'source' => [
                    'url' => $this->episode->image->url
                ],
                'xs' => [
                    'url' => $this->episode->image->url.(($rule = config('image.imageManagerResizingQueryString.xs', false)) ? '?'.$rule : '')
                ],
                'sm' => [
                    'url' => $this->episode->image->url.(($rule = config('image.imageManagerResizingQueryString.sm', false)) ? '?'.$rule : '')
                ],
                'md' => [
                    'url' => $this->episode->image->url.(($rule = config('image.imageManagerResizingQueryString.md', false)) ? '?'.$rule : '')
                ],
                'lg' => [
                    'url' => $this->episode->image->url.(($rule = config('image.imageManagerResizingQueryString.lg', false)) ? '?'.$rule : '')
                ],
                'xl' => [
                    'url' => $this->episode->image->url.(($rule = config('image.imageManagerResizingQueryString.xl', false)) ? '?'.$rule : '')
                ]
            ] : null,
            'image_poster' => $this->episode->imagePoster ? [
                'source' => [
                    'url' => $this->episode->imagePoster->url
                ],
                'xs' => [
                    'url' => $this->episode->imagePoster->url.(($rule = config('image.imagePosterManagerResizingQueryString.xs', false)) ? '?'.$rule : '')
                ],
                'sm' => [
                    'url' => $this->episode->imagePoster->url.(($rule = config('image.imagePosterManagerResizingQueryString.sm', false)) ? '?'.$rule : '')
                ],
                'md' => [
                    'url' => $this->episode->imagePoster->url.(($rule = config('image.imagePosterManagerResizingQueryString.md', false)) ? '?'.$rule : '')
                ],
                'lg' => [
                    'url' => $this->episode->imagePoster->url.(($rule = config('image.imagePosterManagerResizingQueryString.lg', false)) ? '?'.$rule : '')
                ],
                'xl' => [
                    'url' => $this->episode->imagePoster->url.(($rule = config('image.imagePosterManagerResizingQueryString.xl', false)) ? '?'.$rule : '')
                ]
            ] : null,
            'video_full' => ($this->episode->video and $this->episode->video->meride_status == VideoStatus::READY->value) ? [
                'url' => $this->episode->video->url,
                'url_mp4' => $this->episode->video->url_mp4,
                'width' => $this->episode->video->source_width,
                'height' => $this->episode->video->source_height,
                'public' => $this->episode->video->public ? true : false,
                'podcast' => $this->episode->video->podcast ? true : false,
                'meride_embed_id' => $this->episode->video->meride_embed_id,
                'duration' => $this->episode->video->duration,
            ] : null,
            'video_preview' => ($this->episode->videoPreview and $this->episode->videoPreview->meride_status == VideoStatus::READY->value) ? [
                'url' => $this->episode->videoPreview->url,
                'url_mp4' => $this->episode->videoPreview->url_mp4,
                'width' => $this->episode->videoPreview->source_width,
                'height' => $this->episode->videoPreview->source_height,
                'public' => $this->episode->videoPreview->public ? true : false,
                'podcast' => $this->episode->videoPreview->podcast ? true : false,
                'meride_embed_id' => $this->episode->videoPreview->meride_embed_id,
                'duration' => $this->episode->videoPreview->duration,
            ] : null,
            'sharing_link' => [
                'url' => config('public_site.baseUrl').'detail/'.$this->episode->season->program->id.'?episode_id='.$this->episode->id.'&season_id='.$this->episode->season->id,
            ],
        ];

        $db->collection('episodes')->document($this->episode->id)->set($data);

    }
}
