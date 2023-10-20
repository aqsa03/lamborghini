<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\PostStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description'
    ];
}
