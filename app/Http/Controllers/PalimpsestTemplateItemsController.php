<?php

namespace App\Http\Controllers;

use App\Enums\PalimpsestTemplateItemDay;
use App\Models\PalimpsestTemplateItem;
use Illuminate\Http\Request;
use App\Http\Requests\StorePalimpsestTemplateItemRequest;
use App\Models\Image;
use App\Models\Program;
use App\Models\Live;

class PalimpsestTemplateItemsController extends Controller
{
    protected $live;

    public function __construct()
    {
        $this->live = Live::where('podcast', true)->firstOr(function () {
            return redirect()->route('live.index')->with('error', 'No live podcast is present. Please insert one.');
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        foreach(array_column(PalimpsestTemplateItemDay::cases(), 'value') as $day){
            $palimpsestTemplateItems[$day] = PalimpsestTemplateItem::where('day', $day)->get();
        }
        return view('palimpsestTemplateItems.index',[
                'palimpsestTemplateItems' => $palimpsestTemplateItems,
                'request' => $request
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('palimpsestTemplateItems.form', [
            'formType' => 'create',
            'programs' => Program::all(),
            'live' => $this->live,
            'days' => array_column(PalimpsestTemplateItemDay::cases(), 'value')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\StorePalimpsestTemplateItemRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePalimpsestTemplateItemRequest $request)
    {
        $validatedFields = $request->validated();

        if($image = Image::createAndStoreFromRequest($request, 'image', 'palimpsestTemplateItem')){
            $validatedFields['image_id'] = $image->id;
        }

        PalimpsestTemplateItem::create($validatedFields);

        return redirect()->route('palimpsestTemplateItems.index')->with('success','Palimpsest Template Item created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PalimpsestTemplateItem  $palimpsestTemplateItem
     * @return \Illuminate\Http\Response
     */
    public function show(PalimpsestTemplateItem $palimpsestTemplateItem)
    {
        return view('palimpsestTemplateItems.show', [
            'palimpsestTemplateItem' => $palimpsestTemplateItem
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PalimpsestTemplateItem  $palimpsestTemplateItem
     * @return \Illuminate\Http\Response
     */
    public function edit(PalimpsestTemplateItem $palimpsestTemplateItem)
    {
        return view('palimpsestTemplateItems.form', [
            'formType' => 'edit',
            'palimpsestTemplateItem' => $palimpsestTemplateItem,
            'programs' => Program::all(),
            //'lives' => Live::all(),
            'days' => array_column(PalimpsestTemplateItemDay::cases(), 'value')
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\StorePalimpsestTemplateItemRequest  $request
     * @param  \App\Models\PalimpsestTemplateItem  $palimpsestTemplateItem
     * @return \Illuminate\Http\Response
     */
    public function update(StorePalimpsestTemplateItemRequest $request, PalimpsestTemplateItem $palimpsestTemplateItem)
    {
        $validatedFields = $request->validated();
        if($image = Image::createAndStoreFromRequest($request, 'image', 'palimpsestTemplateItem')){
            //TODO rimuovi vecchia immagine se c'è
            $validatedFields['image_id'] = $image->id;
        }

        $palimpsestTemplateItem->update($validatedFields);

        return redirect()->route('palimpsestTemplateItems.index')->with('success','Palimpsest Template Item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PalimpsestTemplateItem  $palimpsestTemplateItem
     * @return \Illuminate\Http\Response
     */
    public function destroy(PalimpsestTemplateItem $palimpsestTemplateItem)
    {
        try{
            $palimpsestTemplateItem->delete();
            return redirect()->route('palimpsestTemplateItems.index')
                        ->with('success','Palimpsest Template Item deleted successfully');
        } catch(\Exception $e) {
            $error_message = $e->getMessage();
            return redirect()->route('palimpsestTemplateItems.index')
                        ->with('error', $error_message);
        }
    }
}
