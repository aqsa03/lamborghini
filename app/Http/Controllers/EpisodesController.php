<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use Illuminate\Http\Request;
use App\Enums\EpisodeStatus;
use App\Http\Requests\StoreEpisodeRequest;
use App\Models\Image;
use App\Models\Season;
use Meride\Storage\Tus\Token;
use App\Models\Video;

class EpisodesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $episodes = Episode::orderBy('season_id')->orderBy('order_number');
        if($request->query('program_id')){
            $episodes->whereIn('season_id', Season::select('id')->where('program_id', '=', $request->query('program_id'))->get());
        }
        if($request->query('season_id')){
            $episodes->where('season_id', '=' , $request->query('season_id'));
        }
        if($request->query('title')){
            foreach( explode(' ', $request->query('title')) as $word){
                $episodes->where('title', 'like' , '%'.$word.'%');
            }
        }
        if($request->query('status') and $request->query('status') != '-1'){
            $episodes->where('status', '=' , $request->query('status'));
        }
        return view('episodes.index',[
                'total' => $episodes->count(),
                'episodes' => $episodes->paginate(20),
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
        $seasons = Season::all();
        if ($seasons->isEmpty()) {
            return redirect()->route('episodes.index')->with('error','No season is present. Please insert at least one.');
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
        return view('episodes.form', [
            'formType' => 'create',
            'seasons' => $seasons,
            'tusToken' => $token,
            'storageUploadEndpoint' => config('meride.storage.uploadEndpoint')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\StoreEpisodeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreEpisodeRequest $request)
    {

        $validatedFields = $request->validated();

        if($image = Image::createAndStoreFromRequest($request, 'image', 'episode')){
            $validatedFields['image_id'] = $image->id;
        }
        if($imagePoster = Image::createAndStoreFromRequest($request, 'image_poster', 'episode')){
            $validatedFields['image_poster_id'] = $imagePoster->id;
        }

        $season = Season::find($validatedFields['season_id']);
        if($video = Video::createFromRequest($request, $image, preview: false, podcast: $season->program->podcast)){
            $validatedFields['video_id'] = $video->id;
        }

        if($video_preview = Video::createFromRequest($request, $image, preview: true, podcast: $season->program->podcast)){
            $validatedFields['video_preview_id'] = $video_preview->id;
        }

        $validatedFields['tags'] = array_filter(array_map('trim', explode(',', $validatedFields['tags'])));
        if($validatedFields['status'] == Episodestatus::PUBLISHED->value){
            $validatedFields['published_at'] = date('Y-m-d H:i:s');
        }

        Episode::create($validatedFields);

        return redirect()->route('episodes.index')->with('success','Episode created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Episode  $episode
     * @return \Illuminate\Http\Response
     */
    public function show(Episode $episode)
    {
        return view('episodes.show', [
            'episode' => $episode
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Episode  $episode
     * @return \Illuminate\Http\Response
     */
    public function edit(Episode $episode)
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
        return view('episodes.form', [
            'formType' => 'edit',
            'episode' => $episode,
            'seasons' => Season::all(),
            'tusToken' => $token,
            'storageUploadEndpoint' => config('meride.storage.uploadEndpoint')
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\StoreEpisodeRequest  $request
     * @param  \App\Models\Episode  $episode
     * @return \Illuminate\Http\Response
     */
    public function update(StoreEpisodeRequest $request, Episode $episode)
    {
        $validatedFields = $request->validated();
        if($image = Image::createAndStoreFromRequest($request, 'image', 'episode')){
            //TODO rimuovi vecchia immagine se c'è
            $validatedFields['image_id'] = $image->id;
        }
        if($imagePoster = Image::createAndStoreFromRequest($request, 'image_poster', 'episode')){
            $validatedFields['image_poster_id'] = $imagePoster->id;
        }

        $season = Season::find($validatedFields['season_id']);
        if($video = Video::createFromRequest($request, $image, preview: false, podcast: $season->program->podcast)){
            //TODO rimuovi vecchio video se c'è
            $validatedFields['video_id'] = $video->id;
        }

        if($video_preview = Video::createFromRequest($request, $image, preview: true, podcast: $season->program->podcast)){
            //TODO rimuovi vecchio video se c'è
            $validatedFields['video_preview_id'] = $video_preview->id;
        }

        $validatedFields['tags'] = array_filter(array_map('trim', explode(',', $validatedFields['tags'])));
        //Sembra esserci un bug di Laravel nella gestione di attributi gestiti nel modello tramite Attribute::make. Anche se si assegna lo stesso valore l'evento updated viene generato
        if($validatedFields['tags'] == $episode->tags){
            unset($validatedFields['tags']);
        }

        if($validatedFields['status'] == EpisodeStatus::PUBLISHED->value and !$episode->published_at){
            $validatedFields['published_at'] = date('Y-m-d H:i:s');
        }
        $episode->update($validatedFields);

        return redirect()->route('episodes.index')->with('success','Episode updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Episode  $episode
     * @return \Illuminate\Http\Response
     */
    public function destroy(Episode $episode)
    {
        try{
            $episode->delete();
            return redirect()->route('episodes.index')
                        ->with('success','Episode deleted successfully');
        } catch(\Exception $e) {
            $error_message = $e->getMessage();
            return redirect()->route('episodes.index')
                        ->with('error', $error_message);
        }
    }
    
    /**
     * Search episodes by their title
     * @param Request The request where must be present the `title` query string attribute
     * @return \Illuminate\Http\Response
     */
    public function searchByTitle(Request $request)
    {
        $title = $request->query("title");
        if (strlen(trim($title)) === 0) return response()->json([]);
        return response()->json(Episode::searchByTitle($title));
    }

    /**
     * Search episodes by their search_string
     * @param Request The request where must be present the `search_string` query string attribute
     * @return \Illuminate\Http\Response
     */
    public function searchByString(Request $request)
    {
        $string = $request->query("string");
        if (strlen(trim($string)) === 0) return response()->json([]);
        return response()->json(Episode::searchByString($string));
    }
}
