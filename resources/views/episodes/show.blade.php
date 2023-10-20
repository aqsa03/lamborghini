@push('scripts')

@vite(['resources/js/episodes/show.js'])

@endpush

<x-layouts.panel_layout>

<div class="body_section">

<a href="{{ route('episodes.index') }}" title="trans('episodes.Episodes list')">&lsaquo; {{ trans('episodes.Back to all episodes') }}</a>

<div class="pr-4 flex gap-4 items-end place-content-end relative w-full">

    <a href="{{ route("episodes.edit", $episode->id) }}" class="btn_new_entity text-center inline-flex items-center" >{{ trans('general.edit') }}</a>

    <form action="{{ route('episodes.destroy', $episode->id) }}" method="POST">
        @csrf
        {{ method_field('DELETE') }}
        <button data-delete-message="Stai per cancellare l'episodio {{ $episode->title }}. Sei sicuro?" class="delete-link delete_btn" type="submit">{{ trans('general.delete') }}</button>
    </form>
</div>

<h1 class="font-bold text-2xl py-8">{{ $episode->title }}</h1>

<div class="md:flex">
    <div class="md:basis-2/3 px-8">

        <div class="w-full mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.description') }}
            </label>
            <div id="description" class="editor_textarea"></div>
            <script type="text/plain" id="source_description">{!! $episode->description !!}</script>
        </div>

        <div class="w-full mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.short_description') }}
            </label>
            <div>
                {{ $episode->short_description }}
            </div>
        </div>

        <div class="w-full mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.tags') }}
            </label>
            <div>
                {{ !empty($episode->tags) ? implode(', ', $episode->tags) : '' }}
            </div>
        </div>

        <div class="w-full mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.image') }}
            </label>
            @if($episode->image)
                <img src="{{ $episode->image->url }}" title="{{ $episode->image->name }}" />
            @endif
        </div>

        <div class="w-full mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.poster image') }}
            </label>
            @if($episode->imagePoster)
                <img src="{{ $episode->imagePoster->url }}" title="{{ $episode->imagePoster->name }}" />
            @endif
        </div>

        <div class="w-full mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.preview video') }}
            </label>
            @include('videos.render', ['entity' => $episode, 'preview' => true])
        </div>

        <div class="w-full mt-12">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.main video') }}
            </label>
            @include('videos.render', ['entity' => $episode, 'preview' => false])
        </div>

    </div>

    <aside class="basis-1/3 border-l p-6">
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.status') }}
            </label>
            <x-badges.episode_status :status="$episode->status">{{ $episode->status }}</x-badges.episode_status>
        </div>
        <div class="aside_info mt-8">
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
        </div>
        @if ($episode->prev_episode?->id)
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('episodes.Prev published episode') }}
            </label>
            <a href="{{ route('episodes.show', $episode->prev_episode?->id) }}" title="{{ $episode->prev_episode?->title }}">{{ $episode->prev_episode?->title }}</a>
        </div>
        @endif
        @if ($episode->next_episode?->id)
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('episodes.Next published episode') }}
            </label>
            <a href="{{ route('episodes.show', $episode->next_episode?->id) }}" title="{{ $episode->next_episode?->title }}">{{ $episode->next_episode?->title }}</a>
        </div>
        @endif
        @if ($episode->status === App\Enums\EpisodeStatus::PUBLISHED->value)
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.published at') }}
            </label>
            <div>
                {{ Carbon\Carbon::createFromDate($episode->published_at)->format("d-m-Y H:i") }}
            </div>
        </div>
        @endif
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.created at') }}
            </label>
            <div>
                {{ Carbon\Carbon::createFromDate($episode->created_at)->format("d-m-Y H:i") }}
            </div>
        </div>
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.updated at') }}
            </label>
            <div>
                {{ Carbon\Carbon::createFromDate($episode->updated_at)->format("d-m-Y H:i") }}
            </div>
        </div>
    </aside>

</div>



</x-layouts.panel_layout>
