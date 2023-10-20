<?php

namespace App\Http\Controllers;

use App\Models\NewsCategory;
use App\Models\News;
use Illuminate\Http\Request;
use App\Enums\NewsStatus;
use App\Http\Requests\StoreNewsRequest;
use App\Models\Image;
use Meride\Storage\Tus\Token;
use App\Models\Video;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $news = News::latest();
        if($request->query('title')){
            foreach( explode(' ', $request->query('title')) as $word){
                $news->where('title', 'like' , '%'.$word.'%');
            }
        }
        if($request->query('status') and $request->query('status') != '-1'){
            $news->where('status', '=' , $request->query('status'));
        }
        return view('news.index',[
                'total' => $news->count(),
                'news' => $news->paginate(20),
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
        $newsCategories = NewsCategory::all();
        if ($newsCategories->isEmpty()) {
            return redirect()->route('news.index')->with('error','No category is present. Please insert at least one.');
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
        return view('news.form', [
            'formType' => 'create',
            'newsCategories' => $newsCategories,
            'tusToken' => $token,
            'storageUploadEndpoint' => config('meride.storage.uploadEndpoint')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\StoreNewsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreNewsRequest $request)
    {

        $validatedFields = $request->validated();

        if($image = Image::createAndStoreFromRequest($request, 'image', 'news')){
            $validatedFields['image_id'] = $image->id;
        }

        if($video = Video::createFromRequest($request, $image, preview: false)){
            $validatedFields['video_id'] = $video->id;
        }

        if($video_preview = Video::createFromRequest($request, $image, preview: true)){
            $validatedFields['video_preview_id'] = $video_preview->id;
        }

        $validatedFields['tags'] = array_filter(array_map('trim', explode(',', $validatedFields['tags'])));
        if($validatedFields['status'] == NewsStatus::PUBLISHED->value){
            $validatedFields['published_at'] = date('Y-m-d H:i:s');
        }

        News::create($validatedFields);

        return redirect()->route('news.index')->with('success','News created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\News  $news
     * @return \Illuminate\Http\Response
     */
    public function show(News $news)
    {
        return view('news.show', [
            'news' => $news
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\News  $news
     * @return \Illuminate\Http\Response
     */
    public function edit(News $news)
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
        return view('news.form', [
            'formType' => 'edit',
            'news' => $news,
            'newsCategories' => NewsCategory::all(),
            'tusToken' => $token,
            'storageUploadEndpoint' => config('meride.storage.uploadEndpoint')
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\StoreNewsRequest  $request
     * @param  \App\Models\News  $news
     * @return \Illuminate\Http\Response
     */
    public function update(StoreNewsRequest $request, News $news)
    {
        $validatedFields = $request->validated();
        if($image = Image::createAndStoreFromRequest($request, 'image', 'news')){
            //TODO rimuovi vecchia immagine se c'è
            $validatedFields['image_id'] = $image->id;
        }
        if($video = Video::createFromRequest($request, $image, preview: false)){
            //TODO rimuovi vecchio video se c'è
            $validatedFields['video_id'] = $video->id;
        }

        if($video_preview = Video::createFromRequest($request, $image, preview: true)){
            //TODO rimuovi vecchio video se c'è
            $validatedFields['video_preview_id'] = $video_preview->id;
        }

        $validatedFields['tags'] = array_filter(array_map('trim', explode(',', $validatedFields['tags'])));
        //Sembra esserci un bug di Laravel nella gestione di attributi gestiti nel modello tramite Attribute::make. Anche se si assegna lo stesso valore l'evento updated viene generato
        if($validatedFields['tags'] == $news->tags){
            unset($validatedFields['tags']);
        }
        
        if($validatedFields['status'] == NewsStatus::PUBLISHED->value and !$news->published_at){
            $validatedFields['published_at'] = date('Y-m-d H:i:s');
        }

        $news->update($validatedFields);

        return redirect()->route('news.index')->with('success','News updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\News  $news
     * @return \Illuminate\Http\Response
     */
    public function destroy(News $news)
    {
        try{
            $news->delete();
            return redirect()->route('news.index')
                        ->with('success','News deleted successfully');
        } catch(\Exception $e) {
            $error_message = $e->getMessage();
            return redirect()->route('news.index')
                        ->with('error', $error_message);
        }
    }
    
    /**
     * Search news by their title
     * @param Request The request where must be present the `title` query string attribute
     * @return \Illuminate\Http\Response
     */
    public function searchByTitle(Request $request)
    {
        $title = $request->query("title");
        if (strlen(trim($title)) === 0) return response()->json([]);
        return response()->json(News::searchByTitle($title));
    }

    /**
     * Search news by their search_string
     * @param Request The request where must be present the `search_string` query string attribute
     * @return \Illuminate\Http\Response
     */
    public function searchByString(Request $request)
    {
        $string = $request->query("string");
        if (strlen(trim($string)) === 0) return response()->json([]);
        return response()->json(News::searchByString($string));
    }
}
