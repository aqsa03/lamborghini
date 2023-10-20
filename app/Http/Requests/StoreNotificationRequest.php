<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\NotificationType;
use App\Enums\NotificationStatus;
use App\Enums\NotificationTopic;

class StoreNotificationRequest extends FormRequest
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
            'message' => 'nullable|string|max:1000',
            'status' => 'required|in:'.implode(',', array_map(function($item) { 
                return $item->value;
            }, NotificationStatus::cases())),
            'type' => 'required|in:'.implode(',', array_map(function($item) { 
                return $item->value;
            }, NotificationType::cases())),
            'topic' => 'required|in:'.implode(',', array_map(function($item) { 
                return $item->value;
            }, NotificationTopic::cases())),
            'scheduled_at' => 'required|date',
            'program_id' => 'required_if:type,==,'.NotificationType::PROGRAM->value.'|nullable|numeric',
            'season_id' => 'required_if:type,==,'.NotificationType::SEASON->value.'|nullable|numeric',
            'episode_id' => 'required_if:type,==,'.NotificationType::EPISODE->value.'|nullable|numeric',
            'live_id' => 'required_if:type,==,'.NotificationType::LIVE->value.'|nullable|numeric',
            'news_id' => 'required_if:type,==,'.NotificationType::NEWS->value.'|nullable|numeric',
        ];
    }
}
