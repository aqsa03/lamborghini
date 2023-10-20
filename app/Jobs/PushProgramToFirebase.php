<?php

namespace App\Jobs;

use App\Models\Program;
use App\Enums\ProgramStatus;
use App\Enums\VideoStatus;
use Illuminate\Bus\Queueable;
use Google\Cloud\Core\Timestamp;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class PushProgramToFirebase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * The program instance.
     *
     * @var \App\Models\Program
     */
    public $program;

    /**
     * Create a new job instance.
     *
     * @param  App\Models\Program  $program
     * @return void
     */
    public function __construct(Program $program)
    {
        $this->program = $program;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $firestore = app('firebase.firestore');
        $db = $firestore->database();

        if(
            $this->program->status != ProgramStatus::PUBLISHED->value or
            !$this->program->published_at
        ){
            throw new \Exception('unable to push unpublished program to firebase');
        }
        $data = [
            'title' => $this->program->title,
            'short_description' => $this->program->short_description,
            'description' => $this->program->descriptionToHtml(),
            'tags' => $this->program->tags,
            'podcast' => $this->program->podcast ? true : false,
            'pay_per_view' => $this->program->pay_per_view ? true : false,
            'price' => $this->program->price,
            'related' => !empty($this->program->related) ? array_map(fn ($id) => [
                'program_id' => (int) $id,
                'ref' => $db->collection('programs')->document($id)
            ], $this->program->related) : null,
            'category' => $this->program->category_id ? $db->collection('categories')->document($this->program->category->id) : null,
            'category_id' => $this->program->category_id ? $this->program->category->id : null,
            'category_title' => $this->program->category_id ? $this->program->category->title : null,
            // 'author' => $db->collection('authors')->document($this->program->author->id),
            // 'author_name' => $this->program->author->name,
            'published_date' => $this->program->published_at ? new Timestamp(new \DateTime($this->program->published_at)) : null,
            //'order_date' => $this->program->ordered_at ? new Timestamp(new \DateTime($this->program->ordered_at)) : null,
            'image' => $this->program->image ? [
                'source' => [
                    'url' => $this->program->image->url
                ],
                'xs' => [
                    'url' => $this->program->image->url.(($rule = config('image.imageManagerResizingQueryString.xs', false)) ? '?'.$rule : '')
                ],
                'sm' => [
                    'url' => $this->program->image->url.(($rule = config('image.imageManagerResizingQueryString.sm', false)) ? '?'.$rule : '')
                ],
                'md' => [
                    'url' => $this->program->image->url.(($rule = config('image.imageManagerResizingQueryString.md', false)) ? '?'.$rule : '')
                ],
                'lg' => [
                    'url' => $this->program->image->url.(($rule = config('image.imageManagerResizingQueryString.lg', false)) ? '?'.$rule : '')
                ],
                'xl' => [
                    'url' => $this->program->image->url.(($rule = config('image.imageManagerResizingQueryString.xl', false)) ? '?'.$rule : '')
                ]
            ] : null,
            'image_poster' => $this->program->imagePoster ? [
                'source' => [
                    'url' => $this->program->imagePoster->url
                ],
                'xs' => [
                    'url' => $this->program->imagePoster->url.(($rule = config('image.imagePosterManagerResizingQueryString.xs', false)) ? '?'.$rule : '')
                ],
                'sm' => [
                    'url' => $this->program->imagePoster->url.(($rule = config('image.imagePosterManagerResizingQueryString.sm', false)) ? '?'.$rule : '')
                ],
                'md' => [
                    'url' => $this->program->imagePoster->url.(($rule = config('image.imagePosterManagerResizingQueryString.md', false)) ? '?'.$rule : '')
                ],
                'lg' => [
                    'url' => $this->program->imagePoster->url.(($rule = config('image.imagePosterManagerResizingQueryString.lg', false)) ? '?'.$rule : '')
                ],
                'xl' => [
                    'url' => $this->program->imagePoster->url.(($rule = config('image.imagePosterManagerResizingQueryString.xl', false)) ? '?'.$rule : '')
                ]
            ] : null,
            'video_full' => ($this->program->video and $this->program->video->meride_status == VideoStatus::READY->value) ? [
                'url' => $this->program->video->url,
                'url_mp4' => $this->program->video->url_mp4,
                'width' => $this->program->video->source_width,
                'height' => $this->program->video->source_height,
                'public' => $this->program->video->public ? true : false,
                'podcast' => $this->program->video->podcast ? true : false,
                'meride_embed_id' => $this->program->video->meride_embed_id,
                'duration' => $this->program->video->duration,
            ] : null,
            'video_preview' => ($this->program->videoPreview and $this->program->videoPreview->meride_status == VideoStatus::READY->value) ? [
                'url' => $this->program->videoPreview->url,
                'url_mp4' => $this->program->videoPreview->url_mp4,
                'width' => $this->program->videoPreview->source_width,
                'height' => $this->program->videoPreview->source_height,
                'public' => $this->program->videoPreview->public ? true : false,
                'podcast' => $this->program->videoPreview->podcast ? true : false,
                'meride_embed_id' => $this->program->videoPreview->meride_embed_id,
                'duration' => $this->program->videoPreview->duration,
            ] : null,
            'sharing_link' => [
                'url' => config('public_site.baseUrl').'detail/'.$this->program->id,
                //'text' =>  'Il coraggio di far vedere cose di cui nessuno parla più. Guarda "'.$this->program->title.'" su Servizio Pubblico'
            ],
        ];

        $db->collection('programs')->document($this->program->id)->set($data);

    }
}
