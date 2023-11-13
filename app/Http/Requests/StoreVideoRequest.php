<?php

namespace App\Http\Requests;

use App\Enums\VideosStatus;
use Illuminate\Foundation\Http\FormRequest;

class StoreVideoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
    

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => 'required|max:255',
            'description' => 'nullable|string',
            'status' => 'in:'.implode(',', array_map(function($item) {
                return $item->value;
            }, VideosStatus::cases())),
            'model_id' => 'numeric',
            'category_id' => 'numeric',
            'image' => 'nullable|image',
            'tags' => 'nullable|string',
            'related' => 'nullable',
            'vod' => 'boolean',
            'is_360' => 'boolean', 
            '360_video'=>'nullable|string',
            'type'=>'nullable|string',
            'video_width' => 'nullable|numeric',
            'video_height' => 'nullable|numeric',
            'video_preview_width' => 'nullable|numeric',
            'video_preview_height' => 'nullable|numeric',
            'pre_existing_video_id'=>'nullable|numeric',
        ];
    }
}
