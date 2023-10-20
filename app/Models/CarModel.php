<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarModel extends Model
{
    use HasFactory;
    protected $table = 'CarModel'; 
    protected $fillable = ['title','description', 'image_id', 'image_poster_id','qr_scan_id','video_id','parent_id'];
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
}
