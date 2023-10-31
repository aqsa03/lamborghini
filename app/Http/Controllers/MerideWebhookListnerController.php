<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Meride\Api;
use App\Models\Video;

class MerideWebhookListnerController extends Controller
{
    public function index(Request $request)
    {
        Log::info("Inside meride webhook for video");
        if(isset($request->event) and $request->event == 'video.available'){
            $video = Video::where('meride_video_id', $request->data['id'])->firstOrFail();
            Log::info("Video to check: ",['video->id'=>$request->data['id']]);
            $video->check_meride_availability();
        }
    }
}
