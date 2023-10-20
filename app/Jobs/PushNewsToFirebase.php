<?php

namespace App\Jobs;

use App\Models\News;
use App\Enums\NewsStatus;
use App\Enums\VideoStatus;
use Illuminate\Bus\Queueable;
use Google\Cloud\Core\Timestamp;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class PushNewsToFirebase implements ShouldQueue
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
     * The news instance.
     *
     * @var \App\Models\News
     */
    public $news;

    /**
     * Create a new job instance.
     *
     * @param  App\Models\News  $news
     * @return void
     */
    public function __construct(News $news)
    {
        $this->news = $news;
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
            $this->news->status != NewsStatus::PUBLISHED->value or
            !$this->news->published_at
        ){
            throw new \Exception('unable to push unpublished news to firebase');
        }
        $data = [
            'title' => $this->news->title,
            'short_description' => $this->news->short_description,
            'description' => $this->news->descriptionToHtml(),
            'tags' => $this->news->tags,
            'news_category' => $this->news->news_category_id ? $db->collection('news_categories')->document($this->news->news_category->id) : null,
            'news_category_id' => $this->news->news_category_id ? $this->news->news_category->id : null,
            'news_category_title' => $this->news->news_category_id ? $this->news->news_category->title : null,
            'published_date' => $this->news->published_at ? new Timestamp(new \DateTime($this->news->published_at)) : null,
            'image' => $this->news->image ? [
                'source' => [
                    'url' => $this->news->image->url
                ],
                'xs' => [
                    'url' => $this->news->image->url.(($rule = config('image.imageManagerResizingQueryString.xs', false)) ? '?'.$rule : '')
                ],
                'sm' => [
                    'url' => $this->news->image->url.(($rule = config('image.imageManagerResizingQueryString.sm', false)) ? '?'.$rule : '')
                ],
                'md' => [
                    'url' => $this->news->image->url.(($rule = config('image.imageManagerResizingQueryString.md', false)) ? '?'.$rule : '')
                ],
                'lg' => [
                    'url' => $this->news->image->url.(($rule = config('image.imageManagerResizingQueryString.lg', false)) ? '?'.$rule : '')
                ],
                'xl' => [
                    'url' => $this->news->image->url.(($rule = config('image.imageManagerResizingQueryString.xl', false)) ? '?'.$rule : '')
                ]
            ] : null,
            'video_full' => ($this->news->video and $this->news->video->meride_status == VideoStatus::READY->value) ? [
                'url' => $this->news->video->url,
                'url_mp4' => $this->news->video->url_mp4,
                'width' => $this->news->video->source_width,
                'height' => $this->news->video->source_height,
                'public' => $this->news->video->public ? true : false,
                'podcast' => $this->news->video->podcast ? true : false,
                'meride_embed_id' => $this->news->video->meride_embed_id,
                'duration' => $this->news->video->duration,
            ] : null,
            'video_preview' => ($this->news->videoPreview and $this->news->videoPreview->meride_status == VideoStatus::READY->value) ? [
                'url' => $this->news->videoPreview->url,
                'url_mp4' => $this->news->videoPreview->url_mp4,
                'width' => $this->news->videoPreview->source_width,
                'height' => $this->news->videoPreview->source_height,
                'public' => $this->news->videoPreview->public ? true : false,
                'podcast' => $this->news->videoPreview->podcast ? true : false,
                'meride_embed_id' => $this->news->videoPreview->meride_embed_id,
                'duration' => $this->news->videoPreview->duration,
            ] : null,
            'sharing_link' => [
                'url' => config('public_site.baseUrl').'news/detail/'.$this->news->id,
                //'text' =>  'Il coraggio di far vedere cose di cui nessuno parla più. Guarda "'.$this->news->title.'" su Servizio Pubblico'
            ],
        ];

        $db->collection('news')->document($this->news->id)->set($data);

    }
}
