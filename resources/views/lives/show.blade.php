@push('scripts')

@vite(['resources/js/lives/show.js'])

@endpush

<x-layouts.panel_layout>

<div class="body_section">

<a href="{{ route('lives.index') }}" title="trans('lives.Lives list')">&lsaquo; {{ trans('lives.Back to all lives') }}</a>

<div class="pr-4 flex gap-4 items-end place-content-end relative w-full">
    @if (Auth::user()->is_root())
        <a href="{{ route('lives.edit', $live->id) }}" class="btn_new_entity text-center inline-flex items-center" >{{ trans('general.edit') }}</a>

        <form action="{{ route('lives.destroy', $live->id) }}" method="POST">
            @csrf
            {{ method_field('DELETE') }}
            <button data-delete-message="Stai per cancellare il live {{ $live->title }}. Sei sicuro?" class="delete-link delete_btn" type="submit">{{ trans('general.delete') }}</button>
        </form>
    @endif
</div>

<h1 class="font-bold text-2xl py-8">{{ $live->title }}</h1>

<div class="md:flex">
    <div class="md:basis-2/3 px-8">

        <!-- <div class="w-full mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.description') }}
            </label>
            <div id="description" class="editor_textarea"></div>
            <script type="text/plain" id="source_description">{!! $live->description !!}</script>
        </div> -->

        <div class="w-full mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.short_description') }}
            </label>
            <div>
                {{ $live->short_description }}
            </div>
        </div>

        <div class="w-full mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.tags') }}
            </label>
            <div>
                {{ !empty($live->tags) ? implode(', ', $live->tags) : '' }}
            </div>
        </div>

        <div class="w-full mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.image') }}
            </label>
            @if($live->image)
            <img src="{{ $live->image->url }}" title="{{ $live->image->name }}" />
            @endif
        </div>

        <div class="w-full mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.poster image') }}
            </label>
            @if($live->imagePoster)
                <img src="{{ $live->imagePoster->url }}" title="{{ $live->imagePoster->name }}" />
            @endif
        </div>

    </div>

    <aside class="basis-1/3 border-l p-6">
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('lives.podcast') }}
            </label>
            {{ trans('general.'.($live->podcast ? 'YES' : 'NO')) }}
        </div>
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('lives.meride embed id') }}
            </label>
            {{ $live->meride_embed_id }}
        </div>
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.created at') }}
            </label>
            <div>
                {{ Carbon\Carbon::createFromDate($live->created_at)->format("d-m-Y H:i") }}
            </div>
        </div>
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.updated at') }}
            </label>
            <div>
                {{ Carbon\Carbon::createFromDate($live->updated_at)->format("d-m-Y H:i") }}
            </div>
        </div>
    </aside>

</div>



</x-layouts.panel_layout>
