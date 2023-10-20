<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Notification;
use App\Models\Live;
use App\Models\Post;
use Kreait\Firebase\Factory;
use App\Services\Firebase\CloudMessagingService;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use App\Enums\NotificationType;
use App\Enums\NotificationStatus;

class PushNotificationToFirebase implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * The post instance.
     *
     * @var \App\Models\Notification
     */
    public $notification;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->notification->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->notification->status != NotificationStatus::SENT->value){
            
            try {
                $messaging = app('firebase.messaging');
                $data = [
                    'title' => $this->notification->title,
                    'message' => $this->notification->message,
                    'type' => $this->notification->type,
                ];
                if($this->notification->type == NotificationType::PROGRAM->value){
                    if($this->notification->program_id){
                        $data['program_id'] = $this->notification->program_id;
                    }
                }
                if($this->notification->type == NotificationType::SEASON->value){
                    if($this->notification->season_id){
                        $data['season_id'] = $this->notification->season_id;
                    }
                }
                if($this->notification->type == NotificationType::EPISODE->value){
                    if($this->notification->episode_id){
                        $data['episode_id'] = $this->notification->episode_id;
                    }
                }
                if($this->notification->type == NotificationType::LIVE->value){
                    if($this->notification->live_id){
                        $data['live_id'] = $this->notification->live_id;
                    }
                }
                if($this->notification->type == NotificationType::NEWS->value){
                    if($this->notification->news_id){
                        $data['news_id'] = $this->notification->news_id;
                    }
                }
                $message = CloudMessage::withTarget('topic', $this->notification->topic)
                            ->withData($data)
                            ->withNotification(FirebaseNotification::create($this->notification->title, $this->notification->message));

                $this->notification->name =  $messaging->send($message);
                $this->notification->status = NotificationStatus::SENT->value;
                $this->notification->save();
            } catch (\Exception $e) {
                $this->notification->status = NotificationStatus::ERROR->value;
                $this->notification->log = $e->getMessage();
                $this->notification->save();
                throw $e;
            }
        }
    }
}
