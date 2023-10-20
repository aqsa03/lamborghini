<?php

namespace App\Http\Controllers;

use Meride\Api;
use App\Models\Live;
use Illuminate\Http\Request;
use App\Http\Requests\StoreLiveRequest;
use App\Models\Image;

class LiveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $lives = Live::latest();
        if($request->query('title')){
            foreach( explode(' ', $request->query('title')) as $word){
                $lives->where('title', 'like' , '%'.$word.'%');
            }
        }
        return view('lives.index',[
                'total' => $lives->count(),
                'lives' => $lives->paginate(20),
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
        $merideApi = new Api(config('meride.authCode'), config('meride.cmsUrl'), 'v2');
        $merideLives = $merideApi->all('live');
        $merideEmbedLives = [];
        foreach($merideLives as $merideLive){
            $embeds = $merideApi->search('embed', [
                'search_live_id' => $merideLive->id
            ]);
            if(!$embeds->isEmpty()){
                array_push($merideEmbedLives, ...$embeds);
            }
        }
        return view('lives.form', [
            'formType' => 'create',
            'merideEmbedLives' => $merideEmbedLives
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\StoreLiveRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLiveRequest $request)
    {

        $validatedFields = $request->validated();

        if($image = Image::createAndStoreFromRequest($request, 'image', 'live')){
            $validatedFields['image_id'] = $image->id;
        }
        if($imagePoster = Image::createAndStoreFromRequest($request, 'image_poster', 'live')){
            $validatedFields['image_poster_id'] = $imagePoster->id;
        }

        $validatedFields['tags'] = array_map('trim', explode(',', $validatedFields['tags']));

        Live::create($validatedFields);

        return redirect()->route('lives.index')->with('success','Live created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $live_id
     * @return \Illuminate\Http\Response
     */
    public function show(int $live_id)
    {
        $live = Live::find($live_id);
        return view('lives.show', [
            'live' => $live
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $live_id
     * @return \Illuminate\Http\Response
     */
    public function edit(int $live_id)
    {
        $live = Live::find($live_id);
        $merideApi = new Api(config('meride.authCode'), config('meride.cmsUrl'), 'v2');
        $merideLives = $merideApi->all('live');
        $merideEmbedLives = [];
        foreach($merideLives as $merideLive){
            $embeds = $merideApi->search('embed', [
                'search_live_id' => $merideLive->id
            ]);
            if(!$embeds->isEmpty()){
                array_push($merideEmbedLives, ...$embeds);
            }
        }
        return view('lives.form', [
            'formType' => 'edit',
            'live' => $live,
            'merideEmbedLives' => $merideEmbedLives
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\StoreLiveRequest  $request
     * @param  int  $live_id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreLiveRequest $request, int $live_id)
    {
        $live = Live::find($live_id);
        $validatedFields = $request->validated();
        if($image = Image::createAndStoreFromRequest($request, 'image', 'live')){
            //TODO rimuovi vecchia immagine se c'è
            $validatedFields['image_id'] = $image->id;
        }
        if($imagePoster = Image::createAndStoreFromRequest($request, 'image_poster', 'live')){
            $validatedFields['image_poster_id'] = $imagePoster->id;
        }

        $validatedFields['tags'] = array_map('trim', explode(',', $validatedFields['tags']));
        //Sembra esserci un bug di Laravel nella gestione di attributi gestiti nel modello tramite Attribute::make. Anche se si assegna lo stesso valore l'evento updated viene generato
        if($validatedFields['tags'] == $live->tags){
            unset($validatedFields['tags']);
        }
        
        $live->update($validatedFields);

        return redirect()->route('lives.index')->with('success','Live updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Live  $live
     * @return \Illuminate\Http\Response
     */
    public function destroy(Live $live)
    {
        try{
            $live->delete();
            return redirect()->route('lives.index')
                        ->with('success','Live deleted successfully');
        } catch(\Exception $e) {
            $error_message = $e->getMessage();
            return redirect()->route('lives.index')
                        ->with('error', $error_message);
        }
    }
    
    /**
     * Search lives by their title
     * @param Request The request where must be present the `title` query string attribute
     * @return \Illuminate\Http\Response
     */
    public function searchByTitle(Request $request)
    {
        $title = $request->query("title");
        if (strlen(trim($title)) === 0) return response()->json([]);
        return response()->json(Live::searchByTitle($title));
    }

    /**
     * Search lives by their search_string
     * @param Request The request where must be present the `search_string` query string attribute
     * @return \Illuminate\Http\Response
     */
    public function searchByString(Request $request)
    {
        $string = $request->query("string");
        if (strlen(trim($string)) === 0) return response()->json([]);
        return response()->json(Live::searchByString($string));
    }
}
