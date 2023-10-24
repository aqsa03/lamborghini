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
        $models = CarModel::latest()
        ->leftJoin('CarModel as parent', 'CarModel.parent_id', '=', 'parent.id')
        ->select('CarModel.*', 'parent.title as parent_id')
        ->paginate(20);
        return view('models.index',[
                'total' => $models->count(),
                'models' => $models
            ])
            ->with('i', (request()->input('page', 1) - 1) * 20);
    }
    public function create()
    {
        $models = CarModel::latest()->whereNull('parent_id')->get();
        $token = '';
        $tokenGenerator = new Token(config('meride.clientId'), config('meride.authCode'));
        try {
            // run the token generation
            $token = $tokenGenerator->generate();
        } catch(\Exception $e) {
            // is important to catch the exception
            throw new \Exception("Some error occured with the video service");
        }
        return view('models.form', [
            'formType' => 'create',
            'tusToken' => $token,
            'models'=>$models,
            'model'=>null,
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
        $validatedFields['parent_id']=$request->parent_id==='null'?null:$request->parent_id;

        CarModel::create($validatedFields);

        return redirect()->route('models.index')->with('success','Model created successfully.');
    }
    public function edit(CarModel $model)
    {
        $models = CarModel::latest()->whereNull('parent_id')->get();
        $token = '';
        $tokenGenerator = new Token(config('meride.clientId'), config('meride.authCode'));
        try {
            // run the token generation
            $token = $tokenGenerator->generate();
        } catch(\Exception $e) {
            // is important to catch the exception
            throw new \Exception("Some error occured with the video service");
        }
        return view('models.form', [
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
        //     'title' => 'required|max:255|unique:CarModel,title,'.$CarModel->id,
        // ]);
        $validatedFields=[];
        $validatedFields['title']=$request->title;
        $validatedFields['description']=$request->description;
        $validatedFields['parent_id']=$request->parent_id==='null'?null:$request->parent_id;
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

        return redirect()->route('models.index')->with('success','Model updated successfully.');
    }
    public function destroy(CarModel $model)
    {

        try{
            $model->delete();
            return redirect()->route('models.index')
                        ->with('success','Model deleted successfully');
        } catch(\Exception $e) {
            $error_message = $e->getMessage();
            if(substr($error_message, 0, 15) == 'SQLSTATE[23000]'){
                preg_match('/`'.config('database.connections.'.config('database.default').'.database').'`.`(.*)`, CONSTRAINT/', $error_message, $matches);
                $error_message = isset($matches[1]) ? 'Unable to delete CarModel with associated '.$matches[1] : 'Unable to delete CarModel with associated entities' ;
            }
            return redirect()->route('models.index')
                        ->with('error', $error_message);
        }
    }


}
