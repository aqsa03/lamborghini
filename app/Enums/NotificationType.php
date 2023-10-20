<?php

namespace App\Enums;

enum NotificationType: String {
    case PROGRAM = "program";
    case SEASON = "season";
    case EPISODE = "episode";
    case LIVE = "live";
    case NEWS = "news";
    
};
