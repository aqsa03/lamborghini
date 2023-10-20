<?php

namespace App\Enums;

enum VideoStatus: String {
    case SAVED = "saved";
    case PENDING = "pending";
    case READY = "ready";
    case ERROR = "error";
};
