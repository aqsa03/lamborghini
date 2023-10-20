<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCarModelRequest;
use Illuminate\Http\Request;
use App\Models\Image;
use App\Models\Video;
use App\Models\CarModel;
use Meride\Storage\Tus\Token;
use App\Http\Requests\StoreProgramRequest;
use Illuminate\Support\Facades\DB;

class CarModelController extends Controller
{
    //
    public function index()
    {
        $models = CarModel::latest();
        return view('model.index',[
                'total' => $models->count(),
                'models' => $models->paginate(20)
            ])
            ->with('i', (request()->input('page', 1) - 1) * 20);
    }
    public function create()
    {
        $model=new CarModel();
        $models = CarModel::all();
        if ($models->isEmpty()) {
            return redirect()->route('model.index')->with('error','No model is present. Please insert at least one.');
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
        return view('model.form', [
            'formType' => 'create',
            'tusToken' => $token,
            'models'=>$models,
            'model'=>$model,
            'storageUploadEndpoint' => config('meride.storage.uploadEndpoint')
        ]);
    }
    public function store(StoreCarModelRequest $request)
    {
        
        $validatedFields=$request->validated();
        if($image = Image::createAndStoreFromRequest($request, 'image', 'model')){
            $validatedFields['image_id'] = $image->id;
        }

        if($imagePoster = Image::createAndStoreFromRequest($request, 'image_poster', 'model')){
            $validatedFields['image_poster_id'] = $imagePoster->id;
        }
        if($QRScan = Image::createAndStoreFromRequest($request, 'qr_scan', 'model')){
            $validatedFields['qr_scan_id'] = $QRScan->id;
        }
        if($video = Video::createFromRequest($request, $image, preview: false)){
            //TODO rimuovi vecchio video se c'è
            $validatedFields['video_id'] = $video->id;
        }

        if($video_preview = Video::createFromRequest($request, $image, preview: true)){
            //TODO rimuovi vecchio video se c'è
            $validatedFields['video_preview_id'] = $video_preview->id;
        }

        CarModel::create($validatedFields);

        return redirect()->route('model.index')->with('success','Model created successfully.');
    }
    public function edit(CarModel $model)
    {
        $models = CarModel::all();
        $token = '';
        $tokenGenerator = new Token(config('meride.clientId'), config('meride.authCode'));
        try {
            // run the token generation
            $token = $tokenGenerator->generate();
        } catch(\Exception $e) {
            // is important to catch the exception
            throw new \Exception("Some error occured with the video service");
        }
        return view('model.form', [
            'formType' => 'edit',
            'model' => $model,
            'models'=>$models,
            'tusToken' => $token,
            'storageUploadEndpoint' => config('meride.storage.uploadEndpoint')
        ]);
    }
    public function update(Request $request, CarModel $model)
    {
        // $request->validate([
        //     'title' => 'required|max:255|unique:CarModel,title,'.$category->id,
        // ]);
        $validatedFields=[];
        $validatedFields['title']=$request->title;
        $validatedFields['description']=$request->description;
        if($image = Image::createAndStoreFromRequest($request, 'image', 'model')){
            $validatedFields['image_id'] = $image->id;
        }

        if($imagePoster = Image::createAndStoreFromRequest($request, 'image_poster', 'model')){
            $validatedFields['image_poster_id'] = $imagePoster->id;
        }
        if($QRScan = Image::createAndStoreFromRequest($request, 'qr_scan', 'model')){
            $validatedFields['qr_scan_id'] = $QRScan->id;
        }
        if($video = Video::createFromRequest($request, $image, preview: false)){
            //TODO rimuovi vecchio video se c'è
            $validatedFields['video_id'] = $video->id;
        }

        if($video_preview = Video::createFromRequest($request, $image, preview: true)){
            //TODO rimuovi vecchio video se c'è
            $validatedFields['video_preview_id'] = $video_preview->id;
        }


        $model->update($validatedFields);

        return redirect()->route('model.index')->with('success','Model updated successfully.');
    }
    public function destroy(CarModel $model)
    {

        try{
            $model->delete();
            return redirect()->route('model.index')
                        ->with('success','Model deleted successfully');
        } catch(\Exception $e) {
            $error_message = $e->getMessage();
            if(substr($error_message, 0, 15) == 'SQLSTATE[23000]'){
                preg_match('/`'.config('database.connections.'.config('database.default').'.database').'`.`(.*)`, CONSTRAINT/', $error_message, $matches);
                $error_message = isset($matches[1]) ? 'Unable to delete category with associated '.$matches[1] : 'Unable to delete category with associated entities' ;
            }
            return redirect()->route('model.index')
                        ->with('error', $error_message);
        }
    }


}
