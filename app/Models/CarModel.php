<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use App\Enums\VideoStatus;
use App\Enums\VideosStatus;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Meride\Api;
use Illuminate\Support\Facades\Log;

class CarModel extends Model
{
    use HasFactory;
    protected $table = 'CarModel'; 
    protected $fillable = ['title','description','image_poster_id','qr_code_id','status','published_at','video_preview_id','parent_id', 'pre_existing_video_id','type', 'ce_model', 'ce_text'];
    public function imagePoster()
    {
        return $this->belongsTo(Image::class, 'image_poster_id');
    }
    public function QRScan()
    {
        return $this->belongsTo(Image::class, 'qr_code_id');
    }
    public function videoPreview()
    {
        return $this->belongsTo(Video::class, 'video_preview_id');
    }
    public function ModelVideo(): HasMany
    {
        return $this->hasMany(ModelVideo::class);
    }

    public function canPublish()
    {
        return $this->videoPreview?->meride_status === VideoStatus::READY->value; 
    }
    public function videosAreReady()
    {
        return $this->videoPreview?->isReady();
    }
    public function videos(): HasMany
    {
        return $this->hasMany(ModelVideo::class);
    }
    public function parentCategory()
    {
        return $this->belongsTo(CarModel::class, 'parent_id');
    }
    public function isPublished()
    {
        return $this->status == VideosStatus::PUBLISHED->value;
    }
    public function get_meride_video()
    {
        Log::info("Get Meride Video details ");
        $video=Video::where('meride_video_id', '=', $this->pre_existing_video_id)->first();
        if(!$video)
        {
            $merideApi = new Api(config('meride.authCode'), config('meride.cmsUrl'), 'v2');
            $videoResponse = $merideApi->get('video', $this->video_preview_id);
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
    public function get_meride_video_by_id()
    {
        Log::info("Get Meride Video details ");
        $video=Video::where('meride_video_id', '=', $this->video_preview_id)->first();
        if(!$video)
        {
            $merideApi = new Api(config('meride.authCode'), config('meride.cmsUrl'), 'v2');
            $videoResponse = $merideApi->search('video', $this->meride_video_id);
            return $videoResponse;
        }
        else{
           return $video;
        }
    }
}
