@push('scripts')
@endpush
<x-layouts.panel_layout>

    <div class="body_section">

        <a href="{{ route('videos.index') }}" title="trans('videos.Videos list')">&lsaquo; {{ trans('videos.Back to all videos') }}</a>

        <div class="pr-4 flex gap-4 items-end place-content-end relative w-full">

            <a href="{{ route("videos.edit", $video->id) }}" class="btn_new_entity text-center inline-flex items-center">{{ trans('general.edit') }}</a>

            <form action="{{ route('videos.destroy', $video->id) }}" method="POST">
                @csrf
                {{ method_field('DELETE') }}
                <button data-delete-message="Stai per cancellare l'episodio {{ $video->title }}. Sei sicuro?" class="delete-link delete_btn" type="submit">{{ trans('general.delete') }}</button>
            </form>
        </div>

        <h1 class="font-bold text-2xl py-8">{{ $video->title }}</h1>

        <div class="md:flex">
            <div class="md:basis-2/3 px-8">

                <div class="w-full mt-12">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                        {{ trans('general.description') }}
                    </label>
                    <div id="description" class="editor_textarea" data-description="{{ $video->description }}">
                        {{ $video->description }}
                    </div>
                    <script type="text/plain" id="source_description">{{ $video->description }}</script>
                </div>
                @if($video->{'360_video'})
                <div class="w-full px-3 mt-12" id="360_video_field">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="360_video">
                        {{ trans("videos.video 360") }}
                    </label>
                    <div style="border: 1px solid #ccc;padding: 8px;cursor: pointer;" onclick="window.location.href='{{ old("{'360_video'}", $video->{'360_video'} ?? '') }}';">
                    {{ $video->{'360_video'} }}
                    </div>
                </div>
                @endif


                @if(!$video->{'360_video'})
                <div class="w-full mt-12">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                        {{ trans('general.preview video') }}
                    </label>
                    @include('videos.render', ['entity' => $video, 'preview' => true])
                </div>
                <div class="w-full mt-12">
                    <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                        {{ trans('general.main video') }}
                    </label>
                    @include('videos.render', ['entity' => $video, 'preview' => true])
                </div>
                @endif
            </div>

            <aside class="basis-1/3 border-l p-6">
                <div class="aside_info mt-8">
                    <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                        {{ trans('general.status') }}
                    </label>
                    <x-badges.episode_status :status="$video->status">{{ $video->status }}</x-badges.episode_status>
                </div>
                @if ($video->status === App\Enums\VideosStatus::PUBLISHED->value)
                <div class="aside_info mt-8">
                    <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                        {{ trans('general.published at') }}
                    </label>
                    <div>
                        {{ Carbon\Carbon::createFromDate($video->published_at)->format("d-m-Y H:i") }}
                    </div>
                </div>
                @endif
                <div class="aside_info mt-8">
                    <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                        {{ trans('general.created at') }}
                    </label>
                    <div>
                        {{ Carbon\Carbon::createFromDate($video->created_at)->format("d-m-Y H:i") }}
                    </div>
                </div>
                <div class="aside_info mt-8">
                    <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                        {{ trans('general.updated at') }}
                    </label>
                    <div>
                        {{ Carbon\Carbon::createFromDate($video->updated_at)->format("d-m-Y H:i") }}
                    </div>
                </div>
            </aside>

        </div>
</x-layouts.panel_layout>