<?php

namespace App\Http\Controllers;

use App\Enums\ModelStatus;
use App\Enums\ModelType;
use App\Http\Requests\StoreCarModelRequest;
use Illuminate\Http\Request;
use App\Models\Image;
use App\Models\Video;
use App\Models\CarModel;
use Meride\Storage\Tus\Token;
use Illuminate\Support\Facades\Log;
use Meride\Api;
use App\Enums\VideoStatus;

class CarModelController extends Controller
{
    //
    public function index()
    {
        $models = CarModel::with('parentCategory')->orderBy('parent_id')->paginate(20);
        return view('models.index', [
            'total' => $models->total(),
            'models' => $models
        ])
            ->with('i', (request()->input('page', 1) - 1) * 20);
    }
    public function create()
    {
        $models = CarModel::latest()->whereNull('parent_id')->get();
        $merideApi = new Api(config('meride.authCode'), config('meride.cmsUrl'), 'v2');
        $sortingCriteria = [
            'field' => 'id',
            'order' => 'desc',
        ];
        $merideLives = $merideApi->all('video', [
            'sort' => $sortingCriteria,
            'perPage' => 10,
        ]);
        $meridePreExisting = [];
        foreach ($merideLives as $merideLive) {
            array_push($meridePreExisting, $merideLive);
        }
        $token = '';
        $tokenGenerator = new Token(config('meride.clientId'), config('meride.authCode'));
        try {
            // run the token generation
            $token = $tokenGenerator->generate();
        } catch (\Exception $e) {
            // is important to catch the exception
            throw new \Exception("Some error occured with the video service");
        }
        return view('models.form', [
            'formType' => 'create',
            'tusToken' => $token,
            'models' => $models,
            'model' => null,
            'meridePreExisting' => $meridePreExisting,
            'storageUploadEndpoint' => config('meride.storage.uploadEndpoint')
        ]);
    }
    public function store(StoreCarModelRequest $request)
    {
        Log::info('Inside Model Store');
        $validatedFields = $request->validated();
        if ($imagePoster = Image::createAndStoreFromRequest($request, 'image_poster', 'model')) {
            $validatedFields['image_poster_id'] = $imagePoster->id;
        }
        if ($QRScan = Image::createAndStoreFromRequest($request, 'qr_code', 'model')) {
            $validatedFields['qr_code_id'] = $QRScan->id;
        }
        $validatedFields['type'] = $validatedFields['type']?$validatedFields['type']:ModelType::NEW->value;
        if ($validatedFields['meride_video_id']) {
            $video = Video::where('meride_video_id', '=', $validatedFields['meride_video_id'])->first();
            if (!$video) {
                $merideApi = new Api(config('meride.authCode'), config('meride.cmsUrl'), 'v2');
                $videoResponse = $merideApi->get('video', $validatedFields['meride_video_id']);
                $embed = $merideApi->create('embed', [
                    'video_id' => $validatedFields['meride_video_id'],
                    'title' => $videoResponse->title,
                    'url' => $videoResponse->url_video,
                    'url_mp4' => $videoResponse->url_video_mp4,
                    'image_preview_url' => $videoResponse->preview_image,
                ]);
                $created_video = Video::create([
                    'title' => $videoResponse->title,
                    'source_url' =>  $videoResponse->url_video,
                    'image_source_url' => $imagePoster ? $imagePoster->url : null,
                    'public' => true,
                    'podcast' => false,
                    'source_width' => $validatedFields['video_width'],
                    'source_height' => $validatedFields['video_height'],
                    'duration' => $videoResponse->duration ?? null,
                    'url' => $videoResponse->url_video,
                    'url_mp4' => $videoResponse->url_video_mp4,
                    'image_preview_url' => $videoResponse->preview_image,
                    'meride_status' => VideoStatus::READY->value,
                    'meride_video_id' => $videoResponse->id,
                    'meride_embed_id' => $embed->public_id ?? $embed->id,
                ]);
                $validatedFields['video_preview_id'] = $created_video->id;
                
            } else {
                $validatedFields['video_preview_id'] = $video->id;
            }
            $validatedFields['pre_existing_video_id']=$validatedFields['meride_video_id'];
            $validatedFields['status'] = ModelStatus::PUBLISHED->value;
        } else if ($video_preview = Video::createFromRequest($request, $imagePoster, preview: true)) {
            //TODO rimuovi vecchio video se c'è
            $validatedFields['video_preview_id'] = $video_preview->id;
        }

        $validatedFields['parent_id'] = $request->parent_id === 'null' ? null : $request->parent_id;
        if ($validatedFields['status'] == ModelStatus::PUBLISHED->value) {
            $validatedFields['published_at'] = date('Y-m-d H:i:s');
        }
        Log::info('Creating a Model with data: ', $validatedFields);
        CarModel::create($validatedFields);
        Log::info('Model created successfully redirecting to list of models');

        return redirect()->route('models.index')->with('success', 'Model created successfully.');
    }
    public function edit(CarModel $model)
    {
        $models = CarModel::latest()->whereNull('parent_id')->get();
        $merideApi = new Api(config('meride.authCode'), config('meride.cmsUrl'), 'v2');
        $sortingCriteria = [
            'field' => 'id',
            'order' => 'desc',
        ];
        $merideLives = $merideApi->all('video', [
            'sort' => $sortingCriteria,
            'perPage' => 10,
        ]);
        $meridePreExisting = [];
        foreach ($merideLives as $merideLive) {
            array_push($meridePreExisting, $merideLive);
        }
        $token = '';
        $tokenGenerator = new Token(config('meride.clientId'), config('meride.authCode'));
        try {
            // run the token generation
            $token = $tokenGenerator->generate();
        } catch (\Exception $e) {
            // is important to catch the exception
            throw new \Exception("Some error occured with the video service");
        }
        return view('models.form', [
            'formType' => 'edit',
            'model' => $model,
            'models' => $models,
            'tusToken' => $token,
            'meridePreExisting' => $meridePreExisting,
            'storageUploadEndpoint' => config('meride.storage.uploadEndpoint')
        ]);
    }
    public function update(StoreCarModelRequest $request, CarModel $model)
    {
        // $request->validate([
        //     'title' => 'required|max:255|unique:CarModel,title,'.$CarModel->id,
        // ]);
        Log::info('Inside Model Update');
        $validatedFields = $request->validated();
        if ($imagePoster = Image::createAndStoreFromRequest($request, 'image_poster', 'model')) {
            $validatedFields['image_poster_id'] = $imagePoster->id;
        }
        if ($QRScan = Image::createAndStoreFromRequest($request, 'qr_code', 'model')) {
            $validatedFields['qr_code_id'] = $QRScan->id;
        }
        if ($video_preview = Video::createFromRequest($request, $imagePoster, preview: true)) {
            //TODO rimuovi vecchio video se c'è
            $validatedFields['video_preview_id'] = $video_preview->id;
        }
        $validatedFields['type'] = ModelType::NEW->value;
        if ($validatedFields['meride_video_id']) {
            $video = Video::where('meride_video_id', '=', $validatedFields['meride_video_id'])->first();
            if (!$video) {
                $merideApi = new Api(config('meride.authCode'), config('meride.cmsUrl'), 'v2');
                $videoResponse = $merideApi->get('video', $validatedFields['meride_video_id']);
                $embed = $merideApi->create('embed', [
                    'video_id' => $validatedFields['meride_video_id'],
                    'title' => $videoResponse->title,
                    'url' => $videoResponse->url_video,
                    'url_mp4' => $videoResponse->url_video_mp4,
                    'image_preview_url' => $videoResponse->preview_image,
                ]);
                $created_video = Video::create([
                    'title' => $videoResponse->title,
                    'source_url' =>  $videoResponse->url_video,
                    'image_source_url' => $imagePoster ? $imagePoster->url : null,
                    'public' => true,
                    'podcast' => false,
                    'source_width' => $validatedFields['video_width'],
                    'source_height' => $validatedFields['video_height'],
                    'duration' => $videoResponse->duration ?? null,
                    'url' => $videoResponse->url_video,
                    'url_mp4' => $videoResponse->url_video_mp4,
                    'image_preview_url' => $videoResponse->preview_image,
                    'meride_status' => VideoStatus::READY->value,
                    'meride_video_id' => $videoResponse->id,
                    'meride_embed_id' => $embed->public_id ?? $embed->id,
                ]);
                $validatedFields['video_preview_id'] = $created_video->id;
                $validatedFields['pre_existing_video_id']=$validatedFields['meride_video_id'];
        }
    }
        $validatedFields['parent_id'] = $request->parent_id === 'null' ? null : $request->parent_id;
        if ($validatedFields['status'] == ModelStatus::PUBLISHED->value) {
            $validatedFields['published_at'] = date('Y-m-d H:i:s');
        }
        Log::info('Updating a Model with data: ', $validatedFields);
        $model->update($validatedFields);
        Log::info('Model updated successfully');

        return redirect()->route('models.index')->with('success', 'Model updated successfully.');
    }
    public function destroy(CarModel $model)
    {

        try {
            $model->delete();
            return redirect()->route('models.index')
                ->with('success', 'Model deleted successfully');
        } catch (\Exception $e) {
            $error_message = $e->getMessage();
            if (substr($error_message, 0, 15) == 'SQLSTATE[23000]') {
                preg_match('/`' . config('database.connections.' . config('database.default') . '.database') . '`.`(.*)`, CONSTRAINT/', $error_message, $matches);
                $error_message = isset($matches[1]) ? 'Unable to delete CarModel with associated ' . $matches[1] : 'Unable to delete CarModel with associated entities';
            }
            return redirect()->route('models.index')
                ->with('error', $error_message);
        }
    }
}
