<?php

namespace App\Models;

use App\Models\Image;
use App\Models\Program;
use App\Models\Live;
use App\Models\Episode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PalimpsestTemplateItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'day',
        'start_at',
        'end_at',
        'live_id',
        'program_id',
        'episode_id',
        'image_id',
    ];

    public function live()
    {
        return $this->belongsTo(Live::class);
    }

    public function episode()
    {
        return $this->belongsTo(Episode::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function image()
    {
        return $this->belongsTo(Image::class, 'image_id');
    }
}
