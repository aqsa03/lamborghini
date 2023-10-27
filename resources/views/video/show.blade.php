@push('scripts')

@vite(['resources/js/episodes/show.js'])

@endpush

<x-layouts.panel_layout>

<div class="body_section">

<a href="{{ route('videos.index') }}" title="trans('videos.Videos list')">&lsaquo; {{ trans('videos.Back to all videos') }}</a>

<div class="pr-4 flex gap-4 items-end place-content-end relative w-full">

    <a href="{{ route("videos.edit", $video->id) }}" class="btn_new_entity text-center inline-flex items-center" >{{ trans('general.edit') }}</a>

    <form action="{{ route('vidoes.destroy', $video->id) }}" method="POST">
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
            <div id="description" class="editor_textarea"></div>
            <script type="text/plain" id="source_description">{!! $video->description !!}</script>
        </div>
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
            @include('videos.render', ['entity' => $video, 'preview' => false])
        </div>

    </div>

    <aside class="basis-1/3 border-l p-6">
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.status') }}
            </label>
            <x-badges.video_status :status="$video->status">{{ $video->status }}</x-badges.video_status>
        </div>
        <!-- <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('episodes.Program') }}
            </label>
            <a href="{{ route('programs.show', $episode->season->program?->id) }}" title="{{ $episode->season->program?->title }}">{{ $episode->season->program?->title }}</a>
        </div>
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('episodes.Season') }}
            </label>
            <a href="{{ route('seasons.show', $episode->season?->id) }}" title="{{ $episode->season?->title }}">{{ $episode->season?->title }}</a>
        </div> -->
        @if ($video->status === App\Enums\Videosstatus::PUBLISHED->value)
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
