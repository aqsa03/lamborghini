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
use Illuminate\Database\Eloquent\Collection;


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
        'ext_view_url',
        'published_at',
        'image_id',
        'video_id',
        'video_preview_id',
        'model_id',
        'category_id',
        'pre_existing_video_id',
        'thumb_num',
        'models',
        'product_video',
        'subtitles',
        'ce_text',
    ];
    protected $attributes = [
        'ce_text' => 'Fuel consumption and emission values of all vehicles promoted on this page*: Fuel consumption combined: 14,1-12,7 l/100km (WLTP); CO2-emissions combined: 325-442 g/km (WLTP); Under approval, not available for sale: Revuelto; Concept car, not available for sale: Asterion, Estoque',
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
     * Interact with the video's models
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function models(): Attribute
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
    public static function searchByTitle(string $title): Collection
    {
        $video = ModelVideo::select('id', 'title');
        foreach( explode(' ', $title) as $word){
            $video->where('title', 'like' , '%'.$word.'%');
        }
        return $video->where("status", "=", VideosStatus::PUBLISHED->value)
                        ->whereNotNull('published_at')
                        ->get();
    }

    /**
     * Search programs by their search_string
     * @param string The search string
     * @return Illuminate\Support\Collection
     */
    public static function searchByString(string $title)
    {
        $video = ModelVideo::with('image');
        foreach( explode(' ', $title) as $word){
            $video->where('title', 'like' , '%'.$word.'%');
        }
        $result = $video->where("status", "=", VideosStatus::PUBLISHED->value)
                        ->whereNotNull('published_at')
                        ->get();

        return $result->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'search_string' => $item->search_string,
                'image_poster' => [
                    'url' => $item->imagePoster->url ?? null
                ]
            ];
        });
    }
    public function get_meride_video()
    {
        Log::info("Get Meride Video details ");
        $video=Video::where('meride_video_id', '=', $this->pre_existing_video_id)->first();
        if(!$video)
        {
            $merideApi = new Api(config('meride.authCode'), config('meride.cmsUrl'), 'v2');
            $videoResponse = $merideApi->get('video', $this->video_id);
            $videoData = [
                'url' => $videoResponse->url_video,
                'url_mp4' => $videoResponse->url_video_mp4,
                'source_width' => $videoResponse->width,
                'source_height' => $videoResponse->height,
                'public' => $videoResponse->public,
                'podcast' => $videoResponse->podcast,
                'meride_embed_id' => $videoResponse->meride_embed_id,
                'duration' => $videoResponse->duration,
            ];
            
            return (object)$videoData;
            
        }
        else{
           return $video;
        }
    }
}
