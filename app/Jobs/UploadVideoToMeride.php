<?php

namespace App\Jobs;

use Meride\Api;
use App\Models\Video;
use App\Enums\VideoStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class UploadVideoToMeride implements ShouldQueue, ShouldBeUnique
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
     * The category instance.
     *
     * @var \App\Models\Video
     */
    public $video;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->video->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->video->meride_status != VideoStatus::SAVED->value){
            $this->fail();
        }

        $merideApi = new Api(config('meride.authCode'), config('meride.cmsUrl'), 'v2');
        $request_param = array(
            'title' => $this->video->title,
            'video' => $this->video->source_url,
            'squeeze_slot' => $this->video->public ? config('meride.encoderSlots.'.($this->video->podcast ? 'podcastPublic' : 'public')) : config('meride.encoderSlots.'.($this->video->podcast ? 'podcastPrivate' : 'private'))
        );
        if($this->video->image_source_url){
            $request_param['preview_image'] = $this->video->image_source_url;
        } else {
            $request_param['snapshot'] = 1;
            $request_param['snapshot_time'] = '00:00:01.000';
        }
        $videoResponse = $merideApi->create('video', $request_param);

        if(!$videoResponse->hasErrors()){
            $this->video->meride_status = VideoStatus::PENDING->value;
            $this->video->meride_video_id = $videoResponse->id;
        } else {
            $this->video->meride_status = VideoStatus::ERROR->value;
            $this->video->log .= "\n".date("Y-m-d H:i:s").' '.$videoResponse->getApiResponse()->error->message;
        }
        $this->video->save();
    }
}
