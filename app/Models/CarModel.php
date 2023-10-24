<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\VideoStatus;

class CarModel extends Model
{
    use HasFactory;
    protected $table = 'CarModel'; 
    protected $fillable = ['title','description', 'image_id', 'image_poster_id','qr_scan_id','video_id','video_preview_id','parent_id'];
    public function image()
    {
        return $this->belongsTo(Image::class, 'image_id');
    }

    public function imagePoster()
    {
        return $this->belongsTo(Image::class, 'image_poster_id');
    }
    public function QRScan()
    {
        return $this->belongsTo(Image::class, 'qr_scan_id');
    }
    public function video()
    {
        return $this->belongsTo(Video::class, 'video_id');
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
    public function videoPreview()
    {
        return $this->belongsTo(Video::class, 'video_preview_id');
    }
    public function videosAreReady()
    {
        return $this->video?->isReady() AND $this->videoPreview?->isReady();
    }
}
