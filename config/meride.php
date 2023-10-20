<?php

return [
    'clientId' => env('MERIDE_CLIENT_ID'),
    'authCode' => env('MERIDE_AUTH_CODE'),
    'cmsUrl' => env('MERIDE_CMS_URL'),
    'webhookSecretKey' => env('MERIDE_WEBHOOK_SECRET_KEY'),
    'encoderSlots' => [
        'public' => env('MERIDE_ENCODER_PUBLIC'),
        'private' => env('MERIDE_ENCODER_PRIVATE'),
        'podcastPublic' => 'podcast_'.env('MERIDE_ENCODER_PUBLIC'),
        'podcastPrivate' => 'podcast_'.env('MERIDE_ENCODER_PRIVATE'),
    ],
    'storage' => [
        'uploadEndpoint' => env('MERIDE_STORAGE_UPLOAD_ENDPOINT', 'https://storageapi.meride.tv/uploads/files')
    ],
    'akamai_video_token_auth_key' => env('AKAMAI_VIDEO_TOKEN_AUTH_KEY'),
];
