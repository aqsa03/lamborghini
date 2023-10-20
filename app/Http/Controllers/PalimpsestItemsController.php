<?php

namespace App\Http\Controllers;

use App\Models\PalimpsestItem;
use Illuminate\Http\Request;
use App\Http\Requests\StorePalimpsestItemRequest;
use App\Models\Image;
use App\Models\Program;
use App\Models\Live;

class PalimpsestItemsController extends Controller
{
    protected $live;

    public function __construct()
    {
        $this->live = Live::where('podcast', false)->firstOr(function () {
            return redirect()->route('live.index')->with('error', 'No TV live is present. Please insert one.');
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $palimpsestItems = PalimpsestItem::orderBy('start_at', 'desc');
        if($request->query('title')){
            foreach( explode(' ', $request->query('title')) as $word){
                $palimpsestItems->where('title', 'like' , '%'.$word.'%');
            }
        }
        return view('palimpsestItems.index',[
                'total' => $palimpsestItems->count(),
                'palimpsestItems' => $palimpsestItems->paginate(20),
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
        return view('palimpsestItems.form', [
            'formType' => 'create',
            'programs' => Program::all(),
            'live' => $this->live,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\StorePalimpsestItemRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePalimpsestItemRequest $request)
    {
        $validatedFields = $request->validated();

        if($image = Image::createAndStoreFromRequest($request, 'image', 'palimpsestItem')){
            $validatedFields['image_id'] = $image->id;
        }

        PalimpsestItem::create($validatedFields);

        return redirect()->route('palimpsestItems.index')->with('success','Palimpsest Item created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PalimpsestItem  $palimpsestItem
     * @return \Illuminate\Http\Response
     */
    public function show(PalimpsestItem $palimpsestItem)
    {
        return view('palimpsestItems.show', [
            'palimpsestItem' => $palimpsestItem
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PalimpsestItem  $palimpsestItem
     * @return \Illuminate\Http\Response
     */
    public function edit(PalimpsestItem $palimpsestItem)
    {
        return view('palimpsestItems.form', [
            'formType' => 'edit',
            'palimpsestItem' => $palimpsestItem,
            'programs' => Program::all(),
            //'lives' => Live::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\StorePalimpsestItemRequest  $request
     * @param  \App\Models\PalimpsestItem  $palimpsestItem
     * @return \Illuminate\Http\Response
     */
    public function update(StorePalimpsestItemRequest $request, PalimpsestItem $palimpsestItem)
    {
        $validatedFields = $request->validated();
        if($image = Image::createAndStoreFromRequest($request, 'image', 'palimpsestItem')){
            //TODO rimuovi vecchia immagine se c'è
            $validatedFields['image_id'] = $image->id;
        }

        $palimpsestItem->update($validatedFields);

        return redirect()->route('palimpsestItems.index')->with('success','Palimpsest Item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PalimpsestItem  $palimpsestItem
     * @return \Illuminate\Http\Response
     */
    public function destroy(PalimpsestItem $palimpsestItem)
    {
        try{
            $palimpsestItem->delete();
            return redirect()->route('palimpsestItems.index')
                        ->with('success','Palimpsest Item deleted successfully');
        } catch(\Exception $e) {
            $error_message = $e->getMessage();
            return redirect()->route('palimpsestItems.index')
                        ->with('error', $error_message);
        }
    }
}
