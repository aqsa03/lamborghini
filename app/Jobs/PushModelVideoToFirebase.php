<?php

namespace App\Jobs;

use App\Enums\VideosStatus;
use App\Models\ModelVideo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Google\Cloud\Core\Timestamp;
use App\Enums\VideoStatus;
use App\Enums\VideoType;
use Illuminate\Support\Facades\Log;

class PushModelVideoToFirebase implements ShouldQueue
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
     * The ModelVideo instance.
     *
     * @var \App\Models\ModelVideo
     */
    public $video;
    public $pre_existing;

    /**
     * Create a new job instance.
     *
     * @param  \App\Models\ModelVideo $video
     * @return void
     * 
     * */
    public function __construct(ModelVideo $video)
    {
        $this->video=$video;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $firestore = app('firebase.firestore');
        $db = $firestore->database();
        if(
            $this->video->status != VideosStatus::PUBLISHED->value
        ){
            Log::info('Video not published yet');
            throw new \Exception('unable to push unpublished video to firebase');
        }
        if($this->video->type=='PRE_EXISTING')
        {
           $this->pre_existing= $this->video->get_meride_video();
        }
        $data = [
            '360' => $this->video->type=='IS_360' ?true:false,
            '360_video'=>$this->video->{'360_video'},
            'category' => $this->video->category_id ? $db->collection('categories')->document($this->video->category_id) : null,
            'category_id'=>$this->video->category_id,
            'description'=>$this->video->description,
            'image' => $this->video->image ? [
                'source' => [
                    'url' => $this->video->image->url
                ],
                'xs' => [
                    'url' => $this->video->image->url.(($rule = config('image.imagePosterManagerResizingQueryString.xs', false)) ? '?'.$rule : '')
                ],
                'sm' => [
                    'url' => $this->video->image->url.(($rule = config('image.imagePosterManagerResizingQueryString.sm', false)) ? '?'.$rule : '')
                ],
                'md' => [
                    'url' => $this->video->image->url.(($rule = config('image.imagePosterManagerResizingQueryString.md', false)) ? '?'.$rule : '')
                ],
                'lg' => [
                    'url' => $this->video->image->url.(($rule = config('image.imagePosterManagerResizingQueryString.lg', false)) ? '?'.$rule : '')
                ],
                'xl' => [
                    'url' => $this->video->image->url.(($rule = config('image.imagePosterManagerResizingQueryString.xl', false)) ? '?'.$rule : '')
                ]
            ] : null,
            'model'=>$this->video->model_id ? $db->collection('carModel')->document($this->video->model_id) : null,
            'model_id'=>$this->video->model_id,
            'published_date'=>$this->video->published_at ? new Timestamp(new \DateTime($this->video->published_at)) : null,
            'related' => !empty($this->video->related) ? array_map(fn ($id) => [
                'video_id' => (int) $id,
                'ref' => $db->collection('video')->document($id)
            ], $this->video->related) : null,
            'tags' => $this->video->tags,
            'title' => $this->video->title, 
            'video' =>  $this->pre_existing? [
                'url' => $this->pre_existing->url,
                'url_mp4' => $this->pre_existing->url_mp4,
                'width' => $this->pre_existing->source_width,
                'height' => $this->pre_existing->source_height,
                'public' => $this->pre_existing->public ? true : false,
                'podcast' => $this->pre_existing->podcast ? true : false,
                'meride_embed_id' => $this->pre_existing->meride_embed_id,
                'duration' => $this->pre_existing->duration,]:
            (($this->video->video and $this->video->video->meride_status == VideoStatus::READY->value) ? [
                'url' => $this->video->video->url,
                'url_mp4' => $this->video->video->url_mp4,
                'width' => $this->video->video->source_width,
                'height' => $this->video->video->source_height,
                'public' => $this->video->video->public ? true : false,
                'podcast' => $this->video->video->podcast ? true : false,
                'meride_embed_id' => $this->video->video->meride_embed_id,
                'duration' => $this->video->video->duration,
            ] : null),
            'vod'=>$this->video->vod?true:false

        ];
        Log::info('Video to save in Firestore:', $data);
        $db->collection('videos')->document($this->video->id)->set($data);
        Log::info('Successfully stored Video in Firestore');

    }
}
