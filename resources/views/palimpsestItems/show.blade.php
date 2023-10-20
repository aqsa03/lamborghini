<x-layouts.panel_layout>

<div class="body_section">

<a href="{{ route('palimpsestItems.index') }}" title="trans('palimpsestItems.PalimpsestItems list')">&lsaquo; {{ trans('palimpsestItems.Back to all palimpsestItems') }}</a>

<div class="pr-4 flex gap-4 items-end place-content-end relative w-full">

    <a href="{{ route("palimpsestItems.edit", $palimpsestItem->id) }}" class="btn_new_entity text-center inline-flex items-center" >{{ trans('general.edit') }}</a>

    <form action="{{ route('palimpsestItems.destroy', $palimpsestItem->id) }}" method="POST">
        @csrf
        {{ method_field('DELETE') }}
        <button data-delete-message="Stai per cancellare la voce {{ $palimpsestItem->title }}. Sei sicuro?" class="delete-link delete_btn" type="submit">{{ trans('general.delete') }}</button>
    </form>
</div>

<h1 class="font-bold text-2xl py-8">{{ $palimpsestItem->title }}</h1>

<div class="md:flex">
    <div class="md:basis-2/3 px-8">

        <div class="w-full mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.start at') }}
            </label>
            {{ Carbon\Carbon::createFromDate($palimpsestItem->start_at)->format("d-m-Y H:i") }}
        </div>

        <div class="w-full mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.end at') }}
            </label>
            {{ Carbon\Carbon::createFromDate($palimpsestItem->end_at)->format("d-m-Y H:i") }}
        </div>

        <div class="w-full mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.description') }}
            </label>
            {{ $palimpsestItem->description }}
        </div>

        <div class="w-full mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.image') }}
            </label>
            @if($palimpsestItem->image)
                <img src="{{ $palimpsestItem->image->url }}" title="{{ $palimpsestItem->image->name }}" />
            @endif
        </div>
    </div>

    <aside class="basis-1/3 border-l p-6">
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('lives.Live') }}
            </label>
            <div>
                {{ $palimpsestItem->live->title }}
            </div>
        </div>

        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('programs.Program') }}
            </label>
            <div>
                {{ $palimpsestItem->program?->title }}
            </div>
        </div>

        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.created at') }}
            </label>
            <div>
                {{ Carbon\Carbon::createFromDate($palimpsestItem->created_at)->format("d-m-Y H:i") }}
            </div>
        </div>
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.updated at') }}
            </label>
            <div>
                {{ Carbon\Carbon::createFromDate($palimpsestItem->updated_at)->format("d-m-Y H:i") }}
            </div>
        </div>
    </aside>

</div>

</x-layouts.panel_layout>
