@if ($entity)
    <?php
    $akamai_token_gen = new App\Services\Akamai\Token\TokenGenerator(config('meride.akamai_video_token_auth_key'));
    $video = $preview ? $entity->videoPreview : $entity->video;
    ?>
    @if (!empty($video))
        @if (empty($video?->url_mp4))
            <div class="flex gap-2 items-center">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#600" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold py-4">{{ $preview ? trans("general.Current preview video is pending") : trans("general.Current video is pending") }}</h3>
            </div>
        @else
            <div class="w-full">
                <video width="540" height="302" src="{{ $video?->url_mp4.(!$video->public ? '?hdnts='.$akamai_token_gen->generate(['start-time' => time() - 5]) : '') }}" controls></video>
            </div>
        @endif
    @endif
@endif