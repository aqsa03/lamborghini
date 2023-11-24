<?php

namespace App\Enums;

enum VideoType: String {
    //case UNSAVED = "UNSAVED";
    case LIVE = "LIVE";
    case EXT_VIEW = "EXT_VIEW";
    case PRE_EXISTING='PRE_EXISTING';
    case NEW='NEW';
};