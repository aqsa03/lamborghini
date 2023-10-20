<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;

class Live extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'short_description',
        'description',
        'tags',
        'podcast',
        'meride_embed_id',
        'url',
        'url_mobile',
        'image_id',
        'image_poster_id',
    ];

    public function image()
    {
        return $this->belongsTo(Image::class, 'image_id');
    }

    public function imagePoster()
    {
        return $this->belongsTo(Image::class, 'image_poster_id');
    }

    /**
     * Interact with the program's tags
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

    public function descriptionToHtml()
    {
        if (empty($this->description)) return "";
        $blocks = json_decode($this->description);
        if (empty($blocks)) return "";
        $html = '';
        foreach ($blocks as $block) {
            switch ($block->type) {
                case 'paragraph':
                    $html .= '<p>' . $block->data->text . '</p>';
                    break;

                case 'header':
                    $html .= '<h'. $block->data->level .'>' . $block->data->text . '</h'. $block->data->level .'>';
                    break;

                case 'raw':
                    $html .= $block->data->html;
                    break;

                case 'list':
                    $lsType = ($block->data->style == 'ordered') ? 'ol' : 'ul';
                    $html .= '<' . $lsType . '>';
                    foreach($block->data->items as $item) {
                        $html .= '<li>' . $item . '</li>';
                    }
                    $html .= '</' . $lsType . '>';
                    break;

                case 'code':
                    $html .= '<pre><code class="language-'. $block->data->lang .'">'. $block->data->code .'</code></pre>';
                    break;

                case 'image':
                    $html .= '<div><img src="'. $block->data->file->url .'" /></div>';
                    break;

                case 'quote':
                    $html .= '<div class="mn_quote_text">'.$block->data->text.'</div><div class="mn_quote_caption">'.$block->data->caption.'</div>';
                    break;

                default:
                    break;
            }
        }
        return $html;
    }

    /**
     * Search lives by their title
     * @param string The title of the program
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function searchByTitle(string $title): Collection
    {
        $live = Live::select('id', 'title');
        foreach( explode(' ', $title) as $word){
            $live->where('title', 'like' , '%'.$word.'%');
        }
        return $live->get();
    }

    /**
     * Search lives by their search_string
     * @param string The search string
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function searchByString(string $title): Collection
    {
        $live = Live::select('id', 'search_string');
        foreach( explode(' ', $title) as $word){
            $live->where('search_string', 'like' , '%'.$word.'%');
        }
        return $live->get();
    }

    /**
     * Generate search string
     * @return string search_String
     */
    public function genSearchString(): string
    {
        return $this->title;
    }


}
