<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }
    public function season()
    {
        return $this->belongsTo(Season::class, 'season_id');
    }
    public function episode()
    {
        return $this->belongsTo(Episode::class, 'episode_id');
    }
    public function live()
    {
        return $this->belongsTo(Live::class, 'live_id');
    }
    public function news()
    {
        return $this->belongsTo(News::class, 'news_id');
    }
}
