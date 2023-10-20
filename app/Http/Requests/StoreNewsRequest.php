<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\NewsStatus;

class StoreNewsRequest extends FormRequest
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
            'short_description' => 'nullable|string',
            'status' => 'in:'.implode(',', array_map(function($item) {
                return $item->value;
            }, NewsStatus::cases())),
            'tags' => 'nullable|string',
            'news_category_id' => 'nullable|numeric',
        ];
    }
}
