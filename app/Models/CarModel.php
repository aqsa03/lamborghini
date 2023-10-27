<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\VideoStatus;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CarModel extends Model
{
    use HasFactory;
    protected $table = 'CarModel'; 
    protected $fillable = ['title','description','image_poster_id','qr_code_id','status','published_at','video_preview_id','parent_id'];
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
        $bothPreviewAndMainVideo = !empty($this->videoPreview) AND !empty($this->video);
        $noPreviewAndMainVideo = empty($this->videoPreview) AND empty($this->video);
        return (
                $noPreviewAndMainVideo == true
                OR
                (
                    $bothPreviewAndMainVideo AND
                    $this->videoPreview?->meride_status === VideoStatus::READY->value AND
                    $this->video?->meride_status === VideoStatus::READY->value
                )
                OR
                (
                    ($this->videoPreview?->meride_status === VideoStatus::READY->value AND empty($this->video)) OR
                    ($this->video?->meride_status === VideoStatus::READY->value AND empty($this->videoPreview))
                )
            );
    }
    public function videosAreReady()
    {
        return $this->video?->isReady() AND $this->videoPreview?->isReady();
    }
    public function videos(): HasMany
    {
        return $this->hasMany(ModelVideo::class);
    }
}
