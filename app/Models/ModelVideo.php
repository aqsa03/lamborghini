<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use App\Enums\VideosStatus;
use App\Enums\VideoStatus;
use App\Models\Image;
use App\Models\Video;
use Meride\Api;
use Illuminate\Support\Facades\Log;

class ModelVideo extends Model
{
    use HasFactory;
    protected $table = 'ModelVideo'; 

    protected $fillable = [
        'title',
        'status',
        'description',
        'tags',
        'related',
        'type',
        '360_video',
        'vod',
        'published_at',
        'image_id',
        'video_id',
        'video_preview_id',
        'model_id',
        'category_id',
        'pre_existing_video_id',
    ];
    public function model()
    {
        return $this->belongsTo(CarModel::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function video()
    {
        return $this->belongsTo(Video::class, 'video_id');
    }

    public function videoPreview()
    {
        return $this->belongsTo(Video::class, 'video_preview_id');
    }
    public function image()
    {
        return $this->belongsTo(Image::class, 'image_id');
    }
    public static function published()
    {
        return ModelVideo::where('status', '=', VideosStatus::PUBLISHED->value);
    }
    public function isPublished()
    {
        return $this->status == VideosStatus::PUBLISHED->value;
    }
    public function canPublish()
    {
        return $this->videoPreview?->meride_status === VideoStatus::READY->value AND $this->video?->meride_status === VideoStatus::READY->value;
    }
    /**
     * Interact with the video's related
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function related(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value),
            set: fn ($value) => json_encode($value),
        );
    }

     /**
     * Interact with the video's tags
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function tags(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value),
            set: fn ($value) => json_encode($value),
        );
    }
    /**
     * Check if both the preview and the main videos are ready
     * @return bool
     */
    public function videosAreReady()
    {
        return $this->video?->isReady() AND $this->videoPreview?->isReady();
    }
    public function get_meride_video()
    {
        Log::info("Get Meride Video details ");
        $video=Video::where('meride_video_id', '=', $this->video_id)->first();
        if(!$video)
        {
            $merideApi = new Api(config('meride.authCode'), config('meride.cmsUrl'), 'v2');
            $videoResponse = $merideApi->get('video', $this->video_id);
            return $videoResponse;
            
        }
        else{
           return $video;
        }
    }
}
