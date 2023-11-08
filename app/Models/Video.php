<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Meride\Api;
use App\Enums\VideoStatus;
use Illuminate\Support\Facades\Log;

class Video extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function isReady()
    {
        return $this->meride_status == VideoStatus::READY->value;
    }

    public function get_associated_entity()
    {
        $program = Program::where('video_id', $this->id)->orWhere('video_preview_id', $this->id)->first();
        if ($program !== null) {
            return $program;
        }

        $season = Season::where('video_id', $this->id)->orWhere('video_preview_id', $this->id)->first();
        if ($season !== null) {
            return $season;
        }

        $episode = Episode::where('video_id', $this->id)->orWhere('video_preview_id', $this->id)->first();
        if ($episode !== null) {
            return $episode;
        }

        $news = News::where('video_id', $this->id)->orWhere('video_preview_id', $this->id)->first();
        if ($news !== null) {
            return $news;
        }

        return false;
    }

    /**
     * Checks if video is available on Meride platform
     *
     * @return  bool
     */
    public function check_meride_availability()
    {
        Log::info("Checking Meride Video availability");
        if (!$this->meride_video_id) {
            Log::info("Video not found");
            return false;
        }
        if ($this->isReady()) {
            Log::info("Video is ready");
            return true;
        }
        $merideApi = new Api(config('meride.authCode'), config('meride.cmsUrl'), 'v2');
        $videoResponse = $merideApi->get('video', $this->meride_video_id);
        Log::info('Video respone from meride Api:', [
            "Meride availability" => $videoResponse->available_video,
            "title" => $videoResponse->title,

        ]);
        if ($videoResponse->available_video) {
            $embed = $merideApi->create('embed', [
                'video_id' => $this->meride_video_id,
                'title' => $videoResponse->title,
                'url' => $videoResponse->url_video,
                'url_mp4' => $videoResponse->url_video_mp4,
                'image_preview_url' => $videoResponse->preview_image,
            ]);
            if (!$embed->hasErrors()) {
                $this->meride_embed_id = $embed->public_id ?? $embed->id;
                $this->url = $videoResponse->url_video;
                $this->url_mp4 = $videoResponse->url_video_mp4;
                $this->image_preview_url = $videoResponse->preview_image;
                $this->meride_status = VideoStatus::READY->value;
                $this->save();
                Log::info('Successfully saved video in databse');
                return true;
            }
            Log::info('Some error occured in embeding video:', $embed->hasErrors());
        }
        return false;
    }

    /**
     * Create Video object from form request
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Models\Image   $poster_image
     * @param  boolean   $preview
     * @param  boolean   $podcast
     * @return \Illuminate\Http\Response
     */
    public static function createFromRequest($request, $poster_image, $preview, $podcast = false)
    {
        $video_input_name = $preview ? 'video_preview_upload_url' : 'video_upload_url';
        $width_input_name = $preview ? 'video_preview_width' : 'video_width';
        $height_input_name = $preview ? 'video_preview_height' : 'video_height';
        $duration_input_name = $preview ? 'video_preview_duration' : 'video_duration';

        if ($request->has($video_input_name) and $request->filled($video_input_name)) {
            return Video::create([
                'title' => $request->title,
                'source_url' => $request->{$video_input_name},
                'image_source_url' => $poster_image ? $poster_image->url : null,
                'meride_status' => VideoStatus::SAVED->value,
                'public' => $preview ? true : false,
                'podcast' => $podcast ? true : false,
                'source_width' => $request->{$width_input_name} ?? 16,
                'source_height' => $request->{$height_input_name} ?? 9,
                'duration' => $request->{$duration_input_name} ?? null,
            ]);
        }
        return false;
    }
    public function get_all_videos()
    {
        Log::info("Checking Meride Video availability");
        $merideApi = new Api(config('meride.authCode'), config('meride.cmsUrl'), 'v2');
        $sortingCriteria = [
            'field' => 'id',
            'order' => 'desc',
        ];

        $videos = $merideApi->all('video', [
            'sort' => $sortingCriteria,
        ]);
        $videoList = [];
        foreach ($videos as $videoData) {

            // Create a Video object or array to store the extracted data
            $video = [
                'title' => $videoData->title,
                'url' => $videoData->url_video,
                'url_mp4' => $videoData->url_video_mp4,
                'image_preview_url' => $videoData->preview_image,
                // Add more properties as needed
            ];
            $videoList[] = $video;
            Log::info("Inner array:", $video);
        }

        return $videoList;
    }
}
