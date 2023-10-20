<?php

namespace App\Enums;

enum PageSectionType: String {
    case MAIN = "main";
    case RULE = "rule";
    case CUSTOM = "custom";
    case NEWS = "news";
    case KEEP_WATCHING = "keep_watching";
};
