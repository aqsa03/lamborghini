<?php

namespace App\Services\Firebase;

use Kreait\Firebase\Contract\Messaging;

class CloudMessagingService
{

    private $messaging;

    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    public function send(\JsonSerializable $message)
    {
        return $this->messaging->send($message);
    }

    public function getMessaging(): Messaging
    {
        return $this->messaging;
    }
}
