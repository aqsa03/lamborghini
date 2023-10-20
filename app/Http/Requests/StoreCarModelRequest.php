<?php

namespace App\Http\Requests;

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
                'title' => 'required|unique:CarModel|max:255',
                'description' => 'nullable|string',          
                'image' => 'nullable|image',
                'parent_id'=>'nullable|numeric',
                'video_width' => 'nullable|numeric',
                'video_height' => 'nullable|numeric',
                'video_preview_width' => 'nullable|numeric',
                'video_preview_height' => 'nullable|numeric',
        ];
    }
}
