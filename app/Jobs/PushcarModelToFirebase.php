<?php

namespace App\Jobs;

use App\Models\CarModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Enums\VideoStatus;
use App\Enums\ModelStatus;
use Google\Cloud\Core\Timestamp;
use Illuminate\Support\Facades\Log;

class PushcarModelToFirebase implements ShouldQueue
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
     * The carModel instance.
     *
     * @var \App\Models\CarModel
     */
    public $model;
    public $pre_existing;

    /**
     * Create a new job instance.
     *
     * @param  \App\Models\CarModel $model
     * @return void
     * 
     * */
    public function __construct(CarModel $model)
    {
        $this->model=$model;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $firestore = app('firebase.firestore');
        $db = $firestore->database();
        if(
            $this->model->status != ModelStatus::PUBLISHED->value or
            !$this->model->published_at
        ){
            Log::info('Model not published yet');

            throw new \Exception('unable to push unpublished model to firebase');
        }
        if($this->model->pre_existing_video_id)
        {
           $this->pre_existing= $this->model->get_meride_video();
        }
        $data = [
            'title' => $this->model->title, 
            'description'=>$this->model->description,
            'parent_id'=>$this->model->parent_id,
            'published_date'=>$this->model->published_at ? new Timestamp(new \DateTime($this->model->published_at)) : null,
            'image' => $this->model->imagePoster ? [
                'source' => [
                    'url' => $this->model->imagePoster->url
                ],
                'xs' => [
                    'url' => $this->model->imagePoster->url.(($rule = config('image.imagePosterManagerResizingQueryString.xs', false)) ? '?'.$rule : '')
                ],
                'sm' => [
                    'url' => $this->model->imagePoster->url.(($rule = config('image.imagePosterManagerResizingQueryString.sm', false)) ? '?'.$rule : '')
                ],
                'md' => [
                    'url' => $this->model->imagePoster->url.(($rule = config('image.imagePosterManagerResizingQueryString.md', false)) ? '?'.$rule : '')
                ],
                'lg' => [
                    'url' => $this->model->imagePoster->url.(($rule = config('image.imagePosterManagerResizingQueryString.lg', false)) ? '?'.$rule : '')
                ],
                'xl' => [
                    'url' => $this->model->imagePoster->url.(($rule = config('image.imagePosterManagerResizingQueryString.xl', false)) ? '?'.$rule : '')
                ]
            ] : null,
            'qr_code' => $this->model->QRScan ? [
                'source' => [
                    'url' => $this->model->QRScan->url
                ],
                'xs' => [
                    'url' => $this->model->QRScan->url.(($rule = config('image.imageManagerResizingQueryString.xs', false)) ? '?'.$rule : '')
                ],
                'sm' => [
                    'url' => $this->model->QRScan->url.(($rule = config('image.imageManagerResizingQueryString.sm', false)) ? '?'.$rule : '')
                ],
                'md' => [
                    'url' => $this->model->QRScan->url.(($rule = config('image.imageManagerResizingQueryString.md', false)) ? '?'.$rule : '')
                ],
                'lg' => [
                    'url' => $this->model->QRScan->url.(($rule = config('image.imageManagerResizingQueryString.lg', false)) ? '?'.$rule : '')
                ],
                'xl' => [
                    'url' => $this->model->QRScan->url.(($rule = config('image.imageManagerResizingQueryString.xl', false)) ? '?'.$rule : '')
                ]
            ] : null,
            'video' =>  $this->pre_existing? [
                'url' => $this->pre_existing->url,
                'url_mp4' => $this->pre_existing->url_mp4,
                'width' => $this->pre_existing->source_width,
                'height' => $this->pre_existing->source_height,
                'public' => $this->pre_existing->public ? true : false,
                'podcast' => $this->pre_existing->podcast ? true : false,
                'meride_embed_id' => $this->pre_existing->meride_embed_id,
                'duration' => $this->pre_existing->duration,]:
            (($this->model->videoPreview and $this->model->videoPreview->meride_status == VideoStatus::READY->value) ? [
                'url' => $this->model->videoPreview->url,
                'url_mp4' => $this->model->videoPreview->url_mp4,
                'width' => $this->model->videoPreview->source_width,
                'height' => $this->model->videoPreview->source_height,
                'public' => $this->model->videoPreview->public ? true : false,
                'podcast' => $this->model->videoPreview->podcast ? true : false,
                'meride_embed_id' => $this->model->videoPreview->meride_embed_id,
                'duration' => $this->model->videoPreview->duration,
            ] : null),
            'ce_model' => !empty($this->model->ce_model) ? array_map(fn ($id) => [
                'model_id' => (int) $id,
                'ref' => $db->collection('models')->document($id)
            ], $this->model->ce_model) : null,
            'parent'=>$this->model->parent_id ? $db->collection('models')->document($this->model->parent_id) : null,

        ];
        Log::info('Model to save in Firestore:', $data);
        $db->collection('models')->document($this->model->id)->set($data);
        Log::info('Successfully stored Model in Firestore');

    }
}
