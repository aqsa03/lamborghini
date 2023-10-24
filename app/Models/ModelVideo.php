<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\VideosStatus;
use App\Enums\VideoStatus;

class ModelVideo extends Model
{
    use HasFactory;
    protected $table = 'ModelVideo'; 
    protected $fillable = [
        'title',
        'status',
        'description',
        'published_at',
        'video_id',
        'video_preview_id',
        'model_id',
    ];
    public function video()
    {
        return $this->belongsTo(Video::class, 'video_id');
    }

    public function videoPreview()
    {
        return $this->belongsTo(Video::class, 'video_preview_id');
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
     * Check if both the preview and the main videos are ready
     * @return bool
     */
    public function videosAreReady()
    {
        return $this->video?->isReady() AND $this->videoPreview?->isReady();
    }
}
