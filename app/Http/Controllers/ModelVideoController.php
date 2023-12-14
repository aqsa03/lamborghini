<?php

namespace App\Http\Controllers;

use App\Enums\VideosStatus;
use App\Enums\VideoType;
use App\Http\Requests\StoreVideoRequest;
use App\Models\ModelVideo;
use Illuminate\Http\Request;
use Meride\Storage\Tus\Token;
use App\Models\CarModel;
use App\Models\Video;
use App\Models\Category;
use App\Models\Image;
use Illuminate\Support\Facades\Log;
use Meride\Api;
use App\Enums\VideoStatus;
use App\Enums\ModelStatus;
use Illuminate\Support\Facades\Http;


class ModelVideoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
    
    $searchTerm = $request->input('search');

    $videos = ModelVideo::orderBy('model_id');

    if ($request->query('model_id')) {
        $videos->where('model_id', '=', $request->query('model_id'));
    }

    if ($searchTerm) {
        foreach (explode(' ', $searchTerm) as $word) {
            $videos->where('title', 'like', '%' . $word . '%');
        }
    }

    if ($request->query('status') and $request->query('status') != '-1') {
        $videos->where('status', '=', $request->query('status'));
    }

    return view('video.index', [
        'total' => $videos->count(),
        'videos' => $videos->paginate(20),
        'request' => $request,
        'searchTerm' => $searchTerm,
    ])->with('i', (request()->input('page', 1) - 1) * 20);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $models = CarModel::all();
        $categories = Category::all();
        if ($categories->isEmpty()) {
            return redirect()->route('videos.index')->with('error', 'No category is present. Please insert at least one.');
        }
        if ($models->isEmpty()) {
            return redirect()->route('videos.index')->with('error', 'No model is present. Please insert at least one.');
        }
        $merideApi = new Api(config('meride.authCode'), config('meride.cmsUrl'), 'v2');
        $meridePreExisting = [];
        $videoResponse = $merideApi->get('embed');
        $total_pages = $videoResponse->last_page;
        $sortingCriteria = [
            'field' => 'id',
            'order' => 'desc',
        ];

        for ($page = 1; $page <= $total_pages; $page++) {
            $merideLives = $merideApi->all('embed', [
                'sort' => $sortingCriteria,
                'search_page' => $page,
            ]);

            foreach ($merideLives as $merideLive) {
                $meridePreExisting[] = $merideLive;
            }
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
        return view('video.form', [
            'formType' => 'create',
            'models' => $models,
            'categories' => $categories,
            'published_videos' => ModelVideo::where('status', '=', VideosStatus::PUBLISHED->value)->orderBy('title')->get(),
            'tusToken' => $token,
            'video' => null,
            'meridePreExisting' => $meridePreExisting,
            'published_ce_models' => CarModel::where('status', '=', ModelStatus::PUBLISHED->value)->whereNotNull('ce_model')->orderBy('title')->get(),
            'storageUploadEndpoint' => config('meride.storage.uploadEndpoint')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\StoreVideoRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreVideoRequest $request)
    {
        Log::info('Inside Create Video Controller');
        $validatedFields = $request->validated();
        if ($image = Image::createAndStoreFromRequest($request, 'image', 'video')) {
            $validatedFields['image_id'] = $image->id;
        }
        if ($validatedFields['type'] === 'EXT_VIEW') {
            $validatedFields['ext_view'] = 1;
            $validatedFields['video_id'] = null;
            $validatedFields['video_preview_id'] = null;
            $validatedFields['status'] = VideosStatus::PUBLISHED->value;
        } else if ($validatedFields['type'] === 'PRE_EXISTING') {
            $video = Video::where('meride_embed_id', '=', $validatedFields['meride_video_id'])->first();
            if (!$video) {
                $merideApi = new Api(config('meride.authCode'), config('meride.cmsUrl'), 'v2');
                $videoResponse = $merideApi->get('embed', $validatedFields['meride_video_id']);
                $subtitles=$videoResponse->subtitles??null;
                $validatedFields['subtitles'] = $subtitles?json_encode([
                    'it' => $subtitles->it,
                    'de' => $subtitles->de,
                    'en' => $subtitles->en,
                    'es' => $subtitles->es,
                    'fr' => $subtitles->fr,
                    'zh' => $subtitles->zh,
                    'ru' => $subtitles->ru,
                    'ja' => $subtitles->ja,
                ]):null;
                $created_video = Video::create([
                    'title' => $videoResponse->title,
                    'source_url' =>  $videoResponse->video->url_video,
                    'image_source_url' => $image ? $image->url : null,
                    'public' => true,
                    'podcast' => false,
                    'source_width' => $videoResponse->width,
                    'source_height' => $videoResponse->height,
                    'duration' => $videoResponse->video->duration ?? null,
                    'url' => $videoResponse->video->url_video ?? null,
                    'url_mp4' => $videoResponse->video->url_video_mp4 ?? null,
                    'image_preview_url' => $videoResponse->video->preview_image ?? null,
                    'meride_status' => VideoStatus::READY->value,
                    'meride_video_id' => $videoResponse->video->id ?? null,
                    'meride_embed_id' => $videoResponse->public_id ?? $videoResponse->id,
                    'subtitles'=>$subtitles?json_encode([
                        'it' => $subtitles->it,
                        'de' => $subtitles->de,
                        'en' => $subtitles->en,
                        'es' => $subtitles->es,
                        'fr' => $subtitles->fr,
                        'zh' => $subtitles->zh,
                        'ru' => $subtitles->ru,
                        'ja' => $subtitles->ja,
                    ]):null,
                ]);
                $validatedFields['video_id'] = $created_video->id;
                $validatedFields['video_preview_id'] = $created_video->id;
            } else {
                $validatedFields['video_id'] = $video->id;
                $validatedFields['video_preview_id'] = $video->id;
                $validatedFields['subtitles']=$video->subtitles;
            }
            $validatedFields['pre_existing_video_id'] = $validatedFields['meride_video_id'];
            $validatedFields['ext_view'] = 0;
            $validatedFields['status'] = VideosStatus::PUBLISHED->value;
        } else if (!$validatedFields['type']) {
            $validatedFields['type'] = VideoType::NEW->value;
            if ($video = Video::createFromRequest($request, $image, preview: true)) {
                $validatedFields['video_id'] = $video->id;
            }

            if ($video_preview = Video::createFromRequest($request, $image, preview: true)) {
                $validatedFields['video_preview_id'] = $video_preview->id;
            }
        }
        $validatedFields['tags'] = array_filter(array_map('trim', explode(',', $validatedFields['tags'])));
        $validatedFields['related'] = $validatedFields['related'] ?? [];
        $validatedFields['models'] = $validatedFields['models'] ?? [];
        if (isset($validatedFields['models']) && !empty($validatedFields['models'])) {
            $modelIds = $validatedFields['models'];
            $modelTitles = CarModel::whereIn('id', $modelIds)->pluck('ce_model');
            $commaSeparatedString = implode(',', $modelTitles->toArray());
            $apiUrl = "https://ce.lamborghini.com/api/v2/consumption_emissions/en/de/{$commaSeparatedString}?_format=json&source=smart_tv";
            $response = Http::get($apiUrl);
            if ($response->successful()) {
                $apiData = $response->json();
                if ($apiData['aggregated'] !== null) {
                    $validatedFields['ce_text'] = $apiData['aggregated']['disclaimer'] ?? null;
                } else {
                    $validatedFields['ce_text'] = $apiData['models'][$commaSeparatedString]['disclaimer'] ?? null;
                }
            } else {
                $validatedFields['ce_text'] = 'Fuel consumption and emission values of all vehicles promoted on this page*: Fuel consumption combined: 14,1-12,7 l/100km (WLTP); CO2-emissions combined: 325-442 g/km (WLTP); Under approval, not available for sale: Revuelto; Concept car, not available for sale: Asterion, Estoque';
            }
        } else {
            $validatedFields['ce_text'] = 'Fuel consumption and emission values of all vehicles promoted on this page*: Fuel consumption combined: 14,1-12,7 l/100km (WLTP); CO2-emissions combined: 325-442 g/km (WLTP); Under approval, not available for sale: Revuelto; Concept car, not available for sale: Asterion, Estoque';
        }
        Log::info('Video to create:', $validatedFields);
        ModelVideo::create($validatedFields);
        Log::info('Video created successfully in database, redirecting to list view');


        return redirect()->route('videos.index')->with('success', 'Video created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ModelVideo  $video
     * @return \Illuminate\Http\Response
     */
    public function show(ModelVideo $video)
    {
        return view('video.show', [

            'video' => $video,
            'published_ce_models' => CarModel::where('status', '=', ModelStatus::PUBLISHED->value)->whereNotNull('ce_model')->orderBy('title')->get(),
            'published_videos' => ModelVideo::where('status', '=', VideosStatus::PUBLISHED->value)->orderBy('title')->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ModelVideo  $video
     * @return \Illuminate\Http\Response
     */
    public function edit(ModelVideo $video)
    {
        $merideApi = new Api(config('meride.authCode'), config('meride.cmsUrl'), 'v2');
        $merideLives = $merideApi->all('video');
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
        return view('video.form', [
            'formType' => 'edit',
            'video' => $video,
            'models' => CarModel::all(),
            'categories' => Category::all(),
            'published_videos' => ModelVideo::where('status', '=', VideosStatus::PUBLISHED->value)->orderBy('title')->get(),
            'tusToken' => $token,
            'meridePreExisting' => $meridePreExisting,
            'published_ce_models' => CarModel::where('status', '=', ModelStatus::PUBLISHED->value)->whereNotNull('ce_model')->orderBy('title')->get(),
            'storageUploadEndpoint' => config('meride.storage.uploadEndpoint')
        ]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\StoreVideoRequest  $request
     * @param  \App\Models\ModelVideo  $ModelVideo
     * @return \Illuminate\Http\Response
     */
    public function update(StoreVideoRequest $request, ModelVideo $video)
    {
        Log::info('Inside Update Video Controller');
        $validatedFields = $request->validated();
        if ($image = Image::createAndStoreFromRequest($request, 'image', 'video')) {
            $validatedFields['image_id'] = $image->id;
        }
        if ($validatedFields['type'] === 'EXT_VIEW') {
            $validatedFields['ext_view'] = 1;
            $validatedFields['video_id'] = null;
            $validatedFields['video_preview_id'] = null;
            $validatedFields['status'] = VideosStatus::PUBLISHED->value;
        } else if ($validatedFields['type'] === 'PRE_EXISTING') {
            $prevideo = Video::where('meride_embed_id', '=', $validatedFields['meride_video_id'])->first();
            if (!$prevideo) {
                $merideApi = new Api(config('meride.authCode'), config('meride.cmsUrl'), 'v2');
                $videoResponse = $merideApi->get('embed', $validatedFields['meride_video_id']);
                $subtitles=$videoResponse->subtitles??null;
                $validatedFields['subtitles'] = $subtitles?json_encode([
                    'it' => $subtitles->it,
                    'de' => $subtitles->de,
                    'en' => $subtitles->en,
                    'es' => $subtitles->es,
                    'fr' => $subtitles->fr,
                    'zh' => $subtitles->zh,
                    'ru' => $subtitles->ru,
                    'ja' => $subtitles->ja,
                ]):null;
                    $created_video = Video::create([
                    'title' => $videoResponse->title,
                    'source_url' =>  $videoResponse->video->url_video,
                    'image_source_url' => $image ? $image->url : null,
                    'public' => true,
                    'podcast' => false,
                    'source_width' => $videoResponse->width,
                    'source_height' => $videoResponse->height,
                    'duration' => $videoResponse->video->duration ?? null,
                    'url' => $videoResponse->video->url_video ?? null,
                    'url_mp4' => $videoResponse->video->url_video_mp4 ?? null,
                    'image_preview_url' => $videoResponse->video->preview_image ?? null,
                    'meride_status' => VideoStatus::READY->value,
                    'meride_video_id' => $videoResponse->video->id ?? null,
                    'meride_embed_id' => $videoResponse->public_id ?? $videoResponse->id,
                    'subtitles'=>$subtitles?json_encode([
                        'it' => $subtitles->it,
                        'de' => $subtitles->de,
                        'en' => $subtitles->en,
                        'es' => $subtitles->es,
                        'fr' => $subtitles->fr,
                        'zh' => $subtitles->zh,
                        'ru' => $subtitles->ru,
                        'ja' => $subtitles->ja,
                    ]):null,
                ]);
                $validatedFields['video_id'] = $created_video->id;
                $validatedFields['video_preview_id'] = $created_video->id;
            } else {
                $validatedFields['video_id'] = $prevideo->id;
                $validatedFields['video_preview_id'] = $prevideo->id;
                $validatedFields['subtitles']=$prevideo->subtitles;
            }
            $validatedFields['pre_existing_video_id'] = $validatedFields['meride_video_id'];
            $validatedFields['ext_view'] = 0;
            $validatedFields['status'] = VideosStatus::PUBLISHED->value;
        } else if (!$validatedFields['type']) {
            $validatedFields['type'] = VideoType::NEW->value;
            
            if ($main_video = Video::createFromRequest($request, $image, preview: true)) {
                //TODO rimuovi vecchio video se c'è
                $validatedFields['video_id'] = $main_video->id;
            }
            if ($video_preview = Video::createFromRequest($request, $image, preview: true)) {
                //TODO rimuovi vecchio video se c'è
                $validatedFields['video_preview_id'] = $video_preview->id;
            }
        }
        $validatedFields['tags'] = array_filter(array_map('trim', explode(',', $validatedFields['tags'])));
        $validatedFields['models'] = $validatedFields['models'] ?? [];
        if (isset($validatedFields['models']) && !empty($validatedFields['models'])) {
            $modelIds = $validatedFields['models'];
            $modelTitles = CarModel::whereIn('id', $modelIds)->pluck('ce_model');
            $commaSeparatedString = implode(',', $modelTitles->toArray());
            $apiUrl = "https://ce.lamborghini.com/api/v2/consumption_emissions/en/de/{$commaSeparatedString}?_format=json&source=smart_tv";
            $response = Http::get($apiUrl);
            if ($response->successful()) {
                $apiData = $response->json();
                if ($apiData['aggregated'] !== null) {
                    $validatedFields['ce_text'] = $apiData['aggregated']['disclaimer'] ?? null;
                } else {
                    $validatedFields['ce_text'] = $apiData['models'][$commaSeparatedString]['disclaimer'] ?? null;
                }
            } else {
                $validatedFields['ce_text'] = 'Fuel consumption and emission values of all vehicles promoted on this page*: Fuel consumption combined: 14,1-12,7 l/100km (WLTP); CO2-emissions combined: 325-442 g/km (WLTP); Under approval, not available for sale: Revuelto; Concept car, not available for sale: Asterion, Estoque';
            }
        } else {
            $validatedFields['ce_text'] = 'Fuel consumption and emission values of all vehicles promoted on this page*: Fuel consumption combined: 14,1-12,7 l/100km (WLTP); CO2-emissions combined: 325-442 g/km (WLTP); Under approval, not available for sale: Revuelto; Concept car, not available for sale: Asterion, Estoque';
        }
        $savedVideo=Video::where('id','=',$video->video_id)->first();
        $validatedFields['subtitles']=$savedVideo->subtitles;
        $validatedFields['related'] = $validatedFields['related'] ?? [];
        if ($validatedFields['status'] == VideosStatus::PUBLISHED->value and !$video->published_at) {
            $validatedFields['published_at'] = date('Y-m-d H:i:s');
        }
        Log::info('Video data to update:', $validatedFields);
        $video->update($validatedFields);
        Log::info('Video updated successfully');

        return redirect()->route('videos.index')->with('success', 'Video updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ModelVideo  $video
     * @return \Illuminate\Http\Response
     */
    public function destroy(ModelVideo $video)
    {
        try {
            $video->delete();
            return redirect()->route('videos.index')
                ->with('success', 'Video deleted successfully');
        } catch (\Exception $e) {
            $error_message = $e->getMessage();
            return redirect()->route('videos.index')
                ->with('error', $error_message);
        }
    }

    /**
     * Search videos by their title
     * @param Request The request where must be present the `title` query string attribute
     * @return \Illuminate\Http\Response
     */
    public function searchByTitle(Request $request)
    {
        $title = $request->query("title");
        if (strlen(trim($title)) === 0) return response()->json([]);
        return response()->json(ModelVideo::searchByTitle($title));
    }

    /**
     * Search videos by their search_string
     * @param Request The request where must be present the `search_string` query string attribute
     * @return \Illuminate\Http\Response
     */
    public function searchByString(Request $request)
    {
        $string = $request->query("string");
        if (strlen(trim($string)) === 0) return response()->json([]);
        return response()->json(ModelVideo::searchByString($string));
    }
}
