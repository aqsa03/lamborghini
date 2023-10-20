<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;
use App\Enums\NotificationStatus;
use App\Jobs\PushNotificationToFirebase;
use Carbon\Carbon;

class scheduleNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Schedule app notifications';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Notification::where('status', '=', NotificationStatus::READY->value)
            ->where('scheduled_at', '<=', Carbon::now())
            ->where('scheduled_at', '>=', Carbon::yesterday())
            ->get()
            ->each(function ($item, $key) {
                PushNotificationToFirebase::dispatch($item);
            });
    }
}
