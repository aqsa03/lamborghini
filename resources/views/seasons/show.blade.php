@push('scripts')

@vite(['resources/js/seasons/show.js'])

@endpush

<x-layouts.panel_layout>

<div class="body_section">

<a href="{{ route('seasons.index') }}" title="trans('seasons.Seasons list')">&lsaquo; {{ trans('seasons.Back to all seasons') }}</a>

<div class="pr-4 flex gap-4 items-end place-content-end relative w-full">

    <a href="{{ route("seasons.edit", $season->id) }}" class="btn_new_entity text-center inline-flex items-center" >{{ trans('general.edit') }}</a>

    <form action="{{ route('seasons.destroy', $season->id) }}" method="POST">
        @csrf
        {{ method_field('DELETE') }}
        <button data-delete-message="Stai per cancellare la stagione {{ $season->title }}. Sei sicuro?" class="delete-link delete_btn" type="submit">{{ trans('general.delete') }}</button>
    </form>
</div>

<h1 class="font-bold text-2xl py-8">{{ $season->title }}</h1>

<div class="md:flex">
    <div class="md:basis-2/3 px-8">

        <div class="w-full mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.description') }}
            </label>
            <div id="description" class="editor_textarea"></div>
            <script type="text/plain" id="source_description">{!! $season->description !!}</script>
        </div>

        <div class="w-full mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.short_description') }}
            </label>
            <div>
                {{ $season->short_description }}
            </div>
        </div>

        <div class="w-full mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.tags') }}
            </label>
            <div>
                {{ !empty($season->tags) ? implode(', ', $season->tags) : '' }}
            </div>
        </div>

        <div class="w-full mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.image') }}
            </label>
            @if($season->imag)
                <img src="{{ $season->image->url }}" title="{{ $season->image->name }}" />
            @endif
        </div>

        <div class="w-full mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.poster image') }}
            </label>
            @if($season->imagePoster)
                <img src="{{ $season->imagePoster->url }}" title="{{ $season->imagePoster->name }}" />
            @endif
        </div>

        <div class="w-full mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.preview video') }}
            </label>
            @include('videos.render', ['entity' => $season, 'preview' => true])
        </div>

        <div class="w-full mt-12">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.main video') }}
            </label>
            @include('videos.render', ['entity' => $season, 'preview' => false])
        </div>

    </div>

    <aside class="basis-1/3 border-l p-6">
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.status') }}
            </label>
            <x-badges.season_status :status="$season->status">{{ $season->status }}</x-badges.season_status>
        </div>
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('seasons.Program') }}
            </label>
            <a href="{{ route('programs.show', $season->program?->id) }}" title="{{ $season->program?->title }}">{{ $season->program?->title }}</a>
        </div>
        @if ($season->status === App\Enums\SeasonStatus::PUBLISHED->value)
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.published at') }}
            </label>
            <div>
                {{ Carbon\Carbon::createFromDate($season->published_at)->format("d-m-Y H:i") }}
            </div>
        </div>
        @endif
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.created at') }}
            </label>
            <div>
                {{ Carbon\Carbon::createFromDate($season->created_at)->format("d-m-Y H:i") }}
            </div>
        </div>
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.updated at') }}
            </label>
            <div>
                {{ Carbon\Carbon::createFromDate($season->updated_at)->format("d-m-Y H:i") }}
            </div>
        </div>
    </aside>

</div>



</x-layouts.panel_layout>
