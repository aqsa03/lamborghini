<?php

namespace App\Enums;

enum NotificationStatus: String {
    case READY = "READY";
    case SENT = "SENT";
    case ERROR = "ERROR";
};
