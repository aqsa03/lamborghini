<?php

namespace App\Enums;

enum VideoType: String {
    //case UNSAVED = "UNSAVED";
    case LIVE = "LIVE";
    case VIDEO_360 = "IS_360";
    case PRE_EXISTING='PRE_EXISTING';
    case NEW='NEW';
};