<?php

namespace App\Models;

use App\Models\Image;
use App\Models\Video;
use App\Models\Season;
use App\Enums\EpisodeStatus;
use App\Enums\SeasonStatus;
use App\Enums\VideoStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Episode extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'status',
        'short_description',
        'description',
        'tags',
        'order_number',
        'published_at',
        'season_id',
        'image_id',
        'image_poster_id',
        'video_id',
        'video_preview_id',
    ];

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function next_episode()
    {
        return $this->belongsTo(Episode::class, 'next_episode_id');
    }

    public function prev_episode()
    {
        return $this->belongsTo(Episode::class, 'prev_episode_id');
    }

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

    public static function published()
    {
        return Episode::where('status', '=', EpisodeStatus::PUBLISHED->value);
    }

    public static function drafts()
    {
        return Episode::where('status', '=', EpisodeStatus::DRAFT->value);
    }

    public static function countDraft()
    {
        return Episode::where('status', '=', EpisodeStatus::DRAFT->value)->count();
    }

    public static function countPublished()
    {
        return Episode::where('status', '=', EpisodeStatus::PUBLISHED->value)->count();
    }

    public function isPublished()
    {
        return $this->status == EpisodeStatus::PUBLISHED->value;
    }

     /**
     * Interact with the season's tags
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
        return $this->videoPreview?->meride_status === VideoStatus::READY->value AND $this->video?->meride_status === VideoStatus::READY->value;
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
     * Search episodes by their title
     * @param string The title of the episode
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function searchByTitle(string $title): Collection
    {
        $episode = Episode::select('id', 'title');
        foreach( explode(' ', $title) as $word){
            $episode->where('title', 'like' , '%'.$word.'%');
        }
        return $episode->where("status", "=", EpisodeStatus::PUBLISHED->value)
                        ->whereNotNull('published_at')
                        ->get();
    }

    /**
     * Search episodes by their search_string
     * @param string The search string
     * @return Illuminate\Support\Collection
     */
    public static function searchByString(string $title)
    {
        $episode = Episode::with('imagePoster');
        foreach( explode(' ', $title) as $word){
            $episode->where('search_string', 'like' , '%'.$word.'%');
        }
        $result = $episode->where("status", "=", EpisodeStatus::PUBLISHED->value)
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
        return $this->season->program->title.' - '.$this->season->title.' - '.$this->title;
    }

    public function findNextPublishedEpisode()
    {
        $episode = Episode::where('season_id', $this->season_id)->where('order_number', $this->order_number + 1)->where('status', EpisodeStatus::PUBLISHED->value)->first();
        if(!$episode){
            $season = Season::where('program_id', $this->season->program_id)->where('order_number', $this->season->order_number + 1)->where('status', SeasonStatus::PUBLISHED->value)->first();
            if($season){
                $episode = Episode::where('season_id', $season->id)->where('order_number', 1)->where('status', EpisodeStatus::PUBLISHED->value)->first();
            }
        }
        return $episode;
    }

    public function findPrevPublishedEpisode()
    {
        $episode = Episode::where('season_id', $this->season_id)->where('order_number', $this->order_number - 1)->where('status', EpisodeStatus::PUBLISHED->value)->first();
        if(!$episode and $this->season->order_number > 1){
            $season = Season::where('program_id', $this->season->program_id)->where('order_number', $this->season->order_number - 1)->where('status', SeasonStatus::PUBLISHED->value)->first();
            if($season){
                $episode = Episode::where('season_id', $season->id)->where('status', EpisodeStatus::PUBLISHED->value)->orderBy('order_number', 'desc')->first();
            }
        }
        return $episode;
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
