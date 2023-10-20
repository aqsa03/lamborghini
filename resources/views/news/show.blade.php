@push('scripts')

@vite(['resources/js/news/show.js'])

@endpush

<x-layouts.panel_layout>

<div class="body_section">

<a href="{{ route('news.index') }}" title="trans('news.News list')">&lsaquo; {{ trans('news.Back to all news') }}</a>

<div class="pr-4 flex gap-4 items-end place-content-end relative w-full">

    <a href="{{ route("news.edit", $news->id) }}" class="btn_new_entity text-center inline-flex items-center" >{{ trans('general.edit') }}</a>

    <form action="{{ route('news.destroy', $news->id) }}" method="POST">
        @csrf
        {{ method_field('DELETE') }}
        <button data-delete-message="Stai per cancellare la news {{ $news->title }}. Sei sicuro?" class="delete-link delete_btn" type="submit">{{ trans('general.delete') }}</button>
    </form>
</div>

<h1 class="font-bold text-2xl py-8">{{ $news->title }}</h1>

<div class="md:flex">
    <div class="md:basis-2/3 px-8">

        <div class="w-full mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.description') }}
            </label>
            <div id="description" class="editor_textarea"></div>
            <script type="text/plain" id="source_description">{!! $news->description !!}</script>
        </div>

        <div class="w-full mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.short_description') }}
            </label>
            <div>
                {{ $news->short_description }}
            </div>
        </div>

        <div class="w-full mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.tags') }}
            </label>
            <div>
                {{ !empty($news->tags) ? implode(', ', $news->tags) : '' }}
            </div>
        </div>

        <div class="w-full mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.image') }}
            </label>
            @if($news->image)
                <img src="{{ $news->image->url }}" title="{{ $news->image->name }}" />
            @endif
        </div>

        <div class="w-full mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.preview video') }}
            </label>
            @include('videos.render', ['entity' => $news, 'preview' => true])
        </div>

        <div class="w-full mt-12">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.main video') }}
            </label>
            @include('videos.render', ['entity' => $news, 'preview' => false])
        </div>

    </div>

    <aside class="basis-1/3 border-l p-6">
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.status') }}
            </label>
            <x-badges.news_status :status="$news->status">{{ $news->status }}</x-badges.news_status>
        </div>
        @if ($news->status === App\Enums\NewsStatus::PUBLISHED->value)
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.published at') }}
            </label>
            <div>
                {{ Carbon\Carbon::createFromDate($news->published_at)->format("d-m-Y H:i") }}
            </div>
        </div>
        @endif
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.created at') }}
            </label>
            <div>
                {{ Carbon\Carbon::createFromDate($news->created_at)->format("d-m-Y H:i") }}
            </div>
        </div>
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.updated at') }}
            </label>
            <div>
                {{ Carbon\Carbon::createFromDate($news->updated_at)->format("d-m-Y H:i") }}
            </div>
        </div>
    </aside>

</div>



</x-layouts.panel_layout>
