<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Program;
use Illuminate\Http\Request;
use App\Enums\ProgramStatus;
use App\Http\Requests\StoreProgramRequest;
use App\Models\Image;
use App\Models\Season;
use Meride\Storage\Tus\Token;
use App\Models\Video;

class ProgramsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $programs = Program::latest();
        if($request->query('title')){
            foreach( explode(' ', $request->query('title')) as $word){
                $programs->where('title', 'like' , '%'.$word.'%');
            }
        }
        if($request->query('status') and $request->query('status') != '-1'){
            $programs->where('status', '=' , $request->query('status'));
        }
        return view('programs.index',[
                'total' => $programs->count(),
                'programs' => $programs->paginate(20),
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
        $categories = Category::all();
        if ($categories->isEmpty()) {
            return redirect()->route('programs.index')->with('error','No category is present. Please insert at least one.');
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
        return view('programs.form', [
            'formType' => 'create',
            'categories' => $categories,
            'tusToken' => $token,
            'published_programs' => Program::where('status', '=', ProgramStatus::PUBLISHED->value)->orderBy('title')->get(),
            'storageUploadEndpoint' => config('meride.storage.uploadEndpoint')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\StoreProgramRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProgramRequest $request)
    {

        $validatedFields = $request->validated();
        if($image = Image::createAndStoreFromRequest($request, 'image', 'program')){
            $validatedFields['image_id'] = $image->id;
        }

        if($imagePoster = Image::createAndStoreFromRequest($request, 'image_poster', 'program')){
            $validatedFields['image_poster_id'] = $imagePoster->id;
        }

        if($video = Video::createFromRequest($request, $image, preview: false, podcast: $validatedFields['podcast'])){
            $validatedFields['video_id'] = $video->id;
        }

        if($video_preview = Video::createFromRequest($request, $image, preview: true, podcast: $validatedFields['podcast'])){
            $validatedFields['video_preview_id'] = $video_preview->id;
        }

        $validatedFields['tags'] = array_filter(array_map('trim', explode(',', $validatedFields['tags'])));
        $validatedFields['related'] = $validatedFields['related'] ?? [];
        if($validatedFields['status'] == ProgramStatus::PUBLISHED->value){
            $validatedFields['published_at'] = date('Y-m-d H:i:s');
        }

        Program::create($validatedFields);

        return redirect()->route('programs.index')->with('success','Program created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Program  $program
     * @return \Illuminate\Http\Response
     */
    public function show(Program $program)
    {
        return view('programs.show', [
            'program' => $program
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Program  $program
     * @return \Illuminate\Http\Response
     */
    public function edit(Program $program)
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
        return view('programs.form', [
            'formType' => 'edit',
            'program' => $program,
            'published_programs' => Program::where('status', '=', ProgramStatus::PUBLISHED->value)->where('id', '!=', $program->id)->orderBy('title')->get(),
            'categories' => Category::all(),
            'tusToken' => $token,
            'storageUploadEndpoint' => config('meride.storage.uploadEndpoint')
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\StoreProgramRequest  $request
     * @param  \App\Models\Program  $program
     * @return \Illuminate\Http\Response
     */
    public function update(StoreProgramRequest $request, Program $program)
    {
        $validatedFields = $request->validated();
        if($image = Image::createAndStoreFromRequest($request, 'image', 'program')){
            //TODO rimuovi vecchia immagine se c'è
            $validatedFields['image_id'] = $image->id;
        }

        if($imagePoster = Image::createAndStoreFromRequest($request, 'image_poster', 'program')){
            $validatedFields['image_poster_id'] = $imagePoster->id;
        }

        if($video = Video::createFromRequest($request, $image, preview: false, podcast: $validatedFields['podcast'])){
            //TODO rimuovi vecchio video se c'è
            $validatedFields['video_id'] = $video->id;
        }

        if($video_preview = Video::createFromRequest($request, $image, preview: true, podcast: $validatedFields['podcast'])){
            //TODO rimuovi vecchio video se c'è
            $validatedFields['video_preview_id'] = $video_preview->id;
        }

        $validatedFields['tags'] = array_filter(array_map('trim', explode(',', $validatedFields['tags'])));
        //Sembra esserci un bug di Laravel nella gestione di attributi gestiti nel modello tramite Attribute::make. Anche se si assegna lo stesso valore l'evento updated viene generato
        if($validatedFields['tags'] == $program->tags){
            unset($validatedFields['tags']);
        }

        $validatedFields['related'] = $validatedFields['related'] ?? [];
        //Sembra esserci un bug di Laravel nella gestione di attributi gestiti nel modello tramite Attribute::make. Anche se si assegna lo stesso valore l'evento updated viene generato
        if($validatedFields['related'] == $program->related){
            unset($validatedFields['related']);
        }

        if($validatedFields['status'] == ProgramStatus::PUBLISHED->value and !$program->published_at){
            $validatedFields['published_at'] = date('Y-m-d H:i:s');
        }

        $program->update($validatedFields);

        return redirect()->route('programs.index')->with('success','Program updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Program  $program
     * @return \Illuminate\Http\Response
     */
    public function destroy(Program $program)
    {
        try{
            $program->delete();
            return redirect()->route('programs.index')
                        ->with('success','Program deleted successfully');
        } catch(\Exception $e) {
            $error_message = $e->getMessage();
            return redirect()->route('programs.index')
                        ->with('error', $error_message);
        }
    }

    /**
     * Search programs by their title
     * @param Request The request where must be present the `title` query string attribute
     * @return \Illuminate\Http\Response
     */
    public function searchByTitle(Request $request)
    {
        $title = $request->query("title");
        if (strlen(trim($title)) === 0) return response()->json([]);
        return response()->json(Program::searchByTitle($title));
    }

    /**
     * Search programs by their search_string
     * @param Request The request where must be present the `search_string` query string attribute
     * @return \Illuminate\Http\Response
     */
    public function searchByString(Request $request)
    {
        $string = $request->query("string");
        if (strlen(trim($string)) === 0) return response()->json([]);
        return response()->json(Program::searchByString($string));
    }

    /**
     * Returns the next season number
     * @param Request The request where must be present the `program_id` query string attribute
     * @return \Illuminate\Http\Response
     */
    public function nextSeasonNumber(Request $request)
    {
        $program_id = $request->query("program_id");
        if ($program_id === null) return response()->json(1);
        return response()->json(($season = Season::where('program_id', $program_id)->orderBy('order_number', 'desc')->first()) ? $season->order_number + 1 : 1);
    }
}
