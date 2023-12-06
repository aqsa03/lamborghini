<?php

namespace App\Http\Requests;

use App\Enums\ModelStatus;
use Illuminate\Foundation\Http\FormRequest;

class StoreCarModelRequest extends FormRequest
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
                'title' => 'required|max:52',
                'description' => 'nullable|string',          
                'image' => 'nullable|image',
                'status' => 'in:'.implode(',', array_map(function($item) {
                    return $item->value;
                }, ModelStatus::cases())),
                'video_width' => 'nullable|numeric',
                'video_height' => 'nullable|numeric',
                'video_preview_width' => 'nullable|numeric',
                'video_preview_height' => 'nullable|numeric',
                'meride_video_id'=>'nullable|numeric',
                'type'=>'nullable|string',
                'ce_model'=>'nullable|string',
        ];
    }
}
