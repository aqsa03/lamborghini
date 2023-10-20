<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\PostStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;

class PageSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'order_number',
        'type',
        'list',
        'rule',
        'page_id'
    ];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Interact with the section's list
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function list(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value),
            set: fn ($value) => json_encode($value),
        );
    }

    /**
     * Interact with the section's rule
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function rule(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value),
            set: fn ($value) => json_encode($value),
        );
    }
}
