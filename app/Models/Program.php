<?php

namespace App\Models;

//use App\Models\User;
use App\Models\Image;
use App\Models\Video;
use App\Models\Category;
use App\Enums\ProgramStatus;
use App\Enums\VideoStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'status',
        'short_description',
        'description',
        'tags',
        'podcast',
        'pay_per_view',
        'price',
        'published_at',
        'related',
        'category_id',
        'image_id',
        'image_poster_id',
        'video_id',
        'video_preview_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // public function author()
    // {
    //     return $this->belongsTo(User::class, 'user_id');
    // }

    public function image()
    {
        return $this->belongsTo(Image::class, 'image_id');
    }

    public function imagePoster()
    {
        return $this->belongsTo(Image::class, 'image_poster_id');
    }

    public function video()
    {
        return $this->belongsTo(Video::class, 'video_id');
    }

    public function videoPreview()
    {
        return $this->belongsTo(Video::class, 'video_preview_id');
    }

    /**
     * Get the seasons for the program.
     */
    public function seasons(): HasMany
    {
        return $this->hasMany(Season::class);
    }

    public function episodes()
    {
        return Episode::whereIn('season_id', $this->seasons->pluck('id')->toArray())->get();
    }

    public static function published()
    {
        return Program::where('status', '=', ProgramStatus::PUBLISHED->value);
    }

    public static function drafts()
    {
        return Program::where('status', '=', ProgramStatus::DRAFT->value);
    }

    public static function countDraft()
    {
        return Program::where('status', '=', ProgramStatus::DRAFT->value)->count();
    }

    public static function countPublished()
    {
        return Program::where('status', '=', ProgramStatus::PUBLISHED->value)->count();
    }

    public function isPublished()
    {
        return $this->status == ProgramStatus::PUBLISHED->value;
    }

    /**
     * Interact with the program's related
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function related(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value),
            set: fn ($value) => json_encode($value),
        );
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
     * Search programs by their title
     * @param string The title of the program
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function searchByTitle(string $title): Collection
    {
        $program = Program::select('id', 'title');
        foreach( explode(' ', $title) as $word){
            $program->where('title', 'like' , '%'.$word.'%');
        }
        return $program->where("status", "=", ProgramStatus::PUBLISHED->value)
                        ->whereNotNull('published_at')
                        ->get();
    }

    /**
     * Search programs by their search_string
     * @param string The search string
     * @return Illuminate\Support\Collection
     */
    public static function searchByString(string $title)
    {
        $program = Program::with('imagePoster');
        foreach( explode(' ', $title) as $word){
            $program->where('search_string', 'like' , '%'.$word.'%');
        }
        $result = $program->where("status", "=", ProgramStatus::PUBLISHED->value)
                        ->whereNotNull('published_at')
                        ->get();

        return $result->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'search_string' => $item->search_string,
                'image_poster' => [
                    'url' => $item->imagePoster->url ?? null
                ]
            ];
        });
    }

    /**
     * Generate search string
     * @return string search_String
     */
    public function genSearchString(): string
    {
        return $this->title;
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
