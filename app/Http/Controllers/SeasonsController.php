<?php

namespace App\Http\Controllers;

use App\Models\Season;
use Illuminate\Http\Request;
use App\Enums\SeasonStatus;
use App\Http\Requests\StoreSeasonRequest;
use App\Models\Episode;
use App\Models\Image;
use App\Models\Program;
use Meride\Storage\Tus\Token;
use App\Models\Video;

class SeasonsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $seasons = Season::orderBy('program_id')->orderBy('order_number');
        if($request->query('program_id')){
            $seasons->where('program_id', '=' , $request->query('program_id'));
        }
        if($request->query('title')){
            foreach( explode(' ', $request->query('title')) as $word){
                $seasons->where('title', 'like' , '%'.$word.'%');
            }
        }
        if($request->query('status') and $request->query('status') != '-1'){
            $seasons->where('status', '=' , $request->query('status'));
        }
        return view('seasons.index',[
                'total' => $seasons->count(),
                'seasons' => $seasons->paginate(20),
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
        $programs = Program::all();
        if ($programs->isEmpty()) {
            return redirect()->route('seasons.index')->with('error','No program is present. Please insert at least one.');
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
        return view('seasons.form', [
            'formType' => 'create',
            'programs' => $programs,
            'tusToken' => $token,
            'storageUploadEndpoint' => config('meride.storage.uploadEndpoint')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\StoreSeasonRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSeasonRequest $request)
    {

        $validatedFields = $request->validated();

        if($image = Image::createAndStoreFromRequest($request, 'image', 'season')){
            $validatedFields['image_id'] = $image->id;
        }

        if($imagePoster = Image::createAndStoreFromRequest($request, 'image_poster', 'season')){
            $validatedFields['image_poster_id'] = $imagePoster->id;
        }

        $program = Program::find($validatedFields['program_id']);
        if($video = Video::createFromRequest($request, $image, preview: false, podcast: $program->podcast)){
            $validatedFields['video_id'] = $video->id;
        }

        if($video_preview = Video::createFromRequest($request, $image, preview: true, podcast: $program->podcast)){
            $validatedFields['video_preview_id'] = $video_preview->id;
        }

        $validatedFields['tags'] = array_filter(array_map('trim', explode(',', $validatedFields['tags'])));
        if($validatedFields['status'] == Seasonstatus::PUBLISHED->value){
            $validatedFields['published_at'] = date('Y-m-d H:i:s');
        }

        Season::create($validatedFields);

        return redirect()->route('seasons.index')->with('success','Season created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Season  $season
     * @return \Illuminate\Http\Response
     */
    public function show(Season $season)
    {
        return view('seasons.show', [
            'season' => $season
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Season  $season
     * @return \Illuminate\Http\Response
     */
    public function edit(Season $season)
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
        return view('seasons.form', [
            'formType' => 'edit',
            'season' => $season,
            'programs' => Program::all(),
            'tusToken' => $token,
            'storageUploadEndpoint' => config('meride.storage.uploadEndpoint')
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\StoreSeasonRequest  $request
     * @param  \App\Models\Season  $season
     * @return \Illuminate\Http\Response
     */
    public function update(StoreSeasonRequest $request, Season $season)
    {
        $validatedFields = $request->validated();
        if($image = Image::createAndStoreFromRequest($request, 'image', 'season')){
            //TODO rimuovi vecchia immagine se c'è
            $validatedFields['image_id'] = $image->id;
        }
        if($imagePoster = Image::createAndStoreFromRequest($request, 'image_poster', 'season')){
            $validatedFields['image_poster_id'] = $imagePoster->id;
        }

        $program = Program::find($validatedFields['program_id']);
        if($video = Video::createFromRequest($request, $image, preview: false, podcast: $program->podcast)){
            //TODO rimuovi vecchio video se c'è
            $validatedFields['video_id'] = $video->id;
        }

        if($video_preview = Video::createFromRequest($request, $image, preview: true, podcast: $program->podcast)){
            //TODO rimuovi vecchio video se c'è
            $validatedFields['video_preview_id'] = $video_preview->id;
        }

        $validatedFields['tags'] = array_filter(array_map('trim', explode(',', $validatedFields['tags'])));
        //Sembra esserci un bug di Laravel nella gestione di attributi gestiti nel modello tramite Attribute::make. Anche se si assegna lo stesso valore l'evento updated viene generato
        if($validatedFields['tags'] == $season->tags){
            unset($validatedFields['tags']);
        }
        
        if($validatedFields['status'] == SeasonStatus::PUBLISHED->value and !$season->published_at){
            $validatedFields['published_at'] = date('Y-m-d H:i:s');
        }

        $season->update($validatedFields);

        return redirect()->route('seasons.index')->with('success','Season updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Season  $season
     * @return \Illuminate\Http\Response
     */
    public function destroy(Season $season)
    {
        try{
            $season->delete();
            return redirect()->route('seasons.index')
                        ->with('success','Season deleted successfully');
        } catch(\Exception $e) {
            $error_message = $e->getMessage();
            return redirect()->route('seasons.index')
                        ->with('error', $error_message);
        }
    }
    
    /**
     * Search seasons by their title
     * @param Request The request where must be present the `title` query string attribute
     * @return \Illuminate\Http\Response
     */
    public function searchByTitle(Request $request)
    {
        $title = $request->query("title");
        if (strlen(trim($title)) === 0) return response()->json([]);
        return response()->json(Season::searchByTitle($title));
    }

    /**
     * Search seasons by their search_string
     * @param Request The request where must be present the `search_string` query string attribute
     * @return \Illuminate\Http\Response
     */
    public function searchByString(Request $request)
    {
        $string = $request->query("string");
        if (strlen(trim($string)) === 0) return response()->json([]);
        return response()->json(Season::searchByString($string));
    }

    /**
     * Returns the next episode number
     * @param Request The request where must be present the `season_id` query string attribute
     * @return \Illuminate\Http\Response
     */
    public function nextEpisodeNumber(Request $request)
    {
        $season_id = $request->query("season_id");
        if ($season_id === null) return response()->json(1);
        return response()->json(($episode = Episode::where('season_id', $season_id)->orderBy('order_number', 'desc')->first()) ? $episode->order_number + 1 : 1);
    }
}
