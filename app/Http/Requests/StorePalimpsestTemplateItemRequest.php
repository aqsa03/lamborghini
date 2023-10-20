<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePalimpsestTemplateItemRequest extends FormRequest
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
            'day' => 'required|string',
            'start_at' => 'required|date_format:H:i',
            'end_at' => 'required|date_format:H:i',
            
            'live_id' => 'nullable|numeric',
            'program_id' => 'nullable|numeric',
        ];
    }
}
