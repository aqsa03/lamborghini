<?php

namespace App\Http\Controllers;

use App\Enums\VideosStatus;
use App\Http\Requests\StoreVideoRequest;
use App\Models\ModelVideo;
use Illuminate\Http\Request;
use Meride\Storage\Tus\Token;
use App\Models\CarModel;
use App\Models\Video;
use App\Models\Category;
use App\Models\Image;
class ModelVideoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $videos = ModelVideo::orderBy('model_id');
        if($request->query('model_id')){
            $videos->where('model_id', '=' , $request->query('model_id'));
        }
        if($request->query('title')){
            foreach( explode(' ', $request->query('title')) as $word){
                $videos->where('title', 'like' , '%'.$word.'%');
            }
        }
        if($request->query('status') and $request->query('status') != '-1'){
            $videos->where('status', '=' , $request->query('status'));
        }
        return view('video.index',[
                'total' => $videos->count(),
                'videos' => $videos->paginate(20),
                'request' => $request
            ])
            ->with('i', (request()->input('page', 1) - 1) * 20);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $models = CarModel::all();
        $categories=Category::all();
        if ($categories->isEmpty()) {
            return redirect()->route('videos.index')->with('error','No category is present. Please insert at least one.');
        }
        if ($models->isEmpty()) {
            return redirect()->route('videos.index')->with('error','No model is present. Please insert at least one.');
        }
       
        $token = '';
        $tokenGenerator = new Token(config('meride.clientId'), config('meride.authCode'));
        try {
            // run the token generation
            $token = $tokenGenerator->generate();
        } catch(\Exception $e) {
            // is important to catch the exception
            throw new \Exception("Some error occured with the video service");
        }
        return view('video.form', [
            'formType' => 'create',
            'models' => $models,
            'categories'=>$categories,
            'published_videos' => ModelVideo::where('status', '=', VideosStatus::PUBLISHED->value)->orderBy('title')->get(),

            'tusToken' => $token,
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
        $validatedFields = $request->validated();
        if($image = Image::createAndStoreFromRequest($request, 'image', 'video')){
            $validatedFields['image_id'] = $image->id;
        }
        if($video = Video::createFromRequest($request,null, preview: true)){
            $validatedFields['video_id'] = $video->id;
        }

        if($video_preview = Video::createFromRequest($request, null, preview: true)){
            $validatedFields['video_preview_id'] = $video_preview->id;
        }
        $validatedFields['tags'] = json_encode(array_filter(array_map('trim', explode(',', $validatedFields['tags']))));
        $validatedFields['related'] = json_encode($validatedFields['related'] ?? []);
        if($validatedFields['status'] == VideosStatus::PUBLISHED->value){
            $validatedFields['published_at'] = date('Y-m-d H:i:s');
        }
        ModelVideo::create($validatedFields);
        

        return redirect()->route('videos.index')->with('success','Video created successfully.');
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
            'video' => $video
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
        $token = '';
        $tokenGenerator = new Token(config('meride.clientId'), config('meride.authCode'));
        try {
            // run the token generation
            $token = $tokenGenerator->generate();
        } catch(\Exception $e) {
            // is important to catch the exception
            throw new \Exception("Some error occured with the video service");
        }
        
        $video->tags=json_decode($video->tags);
        return view('video.form', [
            'formType' => 'edit',
            'video' => $video,
            'models' => CarModel::all(),
            'categories'=>Category::all(),
            'published_videos' => ModelVideo::where('status', '=', VideosStatus::PUBLISHED->value)->orderBy('title')->get(),
            'tusToken' => $token,
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
        $validatedFields = $request->validated();
        if($image = Image::createAndStoreFromRequest($request, 'image', 'video')){
            $validatedFields['image_id'] = $image->id;
        }
        if($main_video = Video::createFromRequest($request, null, preview: true)){
            //TODO rimuovi vecchio video se c'è
            $validatedFields['video_id'] = $main_video->id;
        }
        if($video_preview = Video::createFromRequest($request, null, preview: true)){
            //TODO rimuovi vecchio video se c'è
            $validatedFields['video_preview_id'] = $video_preview->id;
        }
        $validatedFields['tags'] = array_filter(array_map('trim', explode(',', $validatedFields['tags'])));
        $validatedFields['related'] = $validatedFields['related'] ?? [];
        if($validatedFields['status'] == VideosStatus::PUBLISHED->value and !$video->published_at){
            $validatedFields['published_at'] = date('Y-m-d H:i:s');
        }
        $video->update($validatedFields);

        return redirect()->route('videos.index')->with('success','Video updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ModelVideo  $video
     * @return \Illuminate\Http\Response
     */
    public function destroy(ModelVideo $video)
    {
        try{
            $video->delete();
            return redirect()->route('videos.index')
                        ->with('success','Video deleted successfully');
        } catch(\Exception $e) {
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
