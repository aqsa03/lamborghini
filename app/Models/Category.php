<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['title','description', 'image_id', 'parent_id',];

    /**
     * Get the seasons for the program.
     */
    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }
    public function videos(): HasMany
    {
        return $this->hasMany(ModelVideo::class);
    }

    /**
     * Interact with the category's tags
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
    public function image()
    {
        return $this->belongsTo(Image::class, 'image_id');
    }
    public function parentCategory()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
}
