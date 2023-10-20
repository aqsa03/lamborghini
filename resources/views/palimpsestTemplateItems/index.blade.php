<x-layouts.panel_layout>

<div class="body_section">

<x-page_title>{{ trans('palimpsestTemplateItems.Radio palimpest') }}</x-page_title>

<div class="pr-4 flex flex-col items-end relative">
    <div>
        <a href="{{ route('palimpsestTemplateItems.create') }}" title="{{ trans('palimpsestTemplateItems.New palimpsestTemplateItem') }}" class="btn_new_entity">{{ trans('palimpsestTemplateItems.New palimpsestTemplateItem') }}</a>
    </div>
</div>

<div class="mb-4 border-gray-200 dark:border-gray-700">
    <ul class="flex flex-wrap -mb-px text-lg text-center" id="myTab" data-tabs-toggle="#myTabContent" role="tablist">
        <li class="mr-2" role="presentation">
            <button class="inline-block p-4 border-b-2 rounded-t-lg" id="monday-tab" data-tabs-target="#monday" type="button" role="tab" aria-controls="monday" aria-selected="false">{{ trans('general.monday') }}</button>
        </li>
        <li class="mr-2" role="presentation">
            <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="tuesday-tab" data-tabs-target="#tuesday" type="button" role="tab" aria-controls="tuesday" aria-selected="false">{{ trans('general.tuesday') }}</button>
        </li>
        <li class="mr-2" role="presentation">
            <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="wednesday-tab" data-tabs-target="#wednesday" type="button" role="tab" aria-controls="wednesday" aria-selected="false">{{ trans('general.wednesday') }}</button>
        </li>
        <li class="mr-2" role="presentation">
            <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="thursday-tab" data-tabs-target="#thursday" type="button" role="tab" aria-controls="thursday" aria-selected="false">{{ trans('general.thursday') }}</button>
        </li>
        <li class="mr-2" role="presentation">
            <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="friday-tab" data-tabs-target="#friday" type="button" role="tab" aria-controls="friday" aria-selected="false">{{ trans('general.friday') }}</button>
        </li>
        <li class="mr-2" role="presentation">
            <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="saturday-tab" data-tabs-target="#saturday" type="button" role="tab" aria-controls="saturday" aria-selected="false">{{ trans('general.saturday') }}</button>
        </li>
        <li role="presentation">
            <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="sunday-tab" data-tabs-target="#sunday" type="button" role="tab" aria-controls="sunday" aria-selected="false">{{ trans('general.sunday') }}</button>
        </li>
    </ul>
</div>
<div id="myTabContent">
    @foreach ($palimpsestTemplateItems as $day => $ps)
    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="{{ $day }}" role="tabpanel" aria-labelledby="{{ $day }}-tab">
        <ol class="table_list"> 
            <li class="table_list__row">
                <div class="table_list__row__header_cell">
                    {{ trans('general.title') }}
                </div>
                <div class="table_list__row__header_cell">
                    {{ trans('palimpsestTemplateItems.day') }}
                </div>
                <div class="table_list__row__header_cell">
                    {{ trans('general.start at') }}
                </div>
                <div class="table_list__row__header_cell">
                    {{ trans('general.end at') }}
                </div>
                <div class="table_list__row__header_cell_action">
                    {{ trans('general.edit') }}
                </div>
                <div class="table_list__row__header_cell_action">
                    {{ trans('general.delete') }}
                </div>
            </li>   
            @foreach ($ps as $palimpsestTemplateItem)    
                <li class="table_list__row hover:bg-slate-100">
                    <div class="table_list__row__cell">
                        <a class="table_list__row__cell__title" href="{{ route('palimpsestTemplateItems.show', $palimpsestTemplateItem->id) }}" title="{{ $palimpsestTemplateItem->title }}">{{ $palimpsestTemplateItem->title }}</a>
                    </div>
                    <div class="table_list__row__cell">
                        {{ $palimpsestTemplateItem->day }}
                    </div>
                    <div class="table_list__row__cell">
                        {{ Carbon\Carbon::createFromDate($palimpsestTemplateItem->start_at)->format("H:i") }}
                    </div>
                    <div class="table_list__row__cell">
                        {{ Carbon\Carbon::createFromDate($palimpsestTemplateItem->end_at)->format("H:i") }}
                    </div>
                    @if (Auth::user()->is_root())
                        <div class="table_list__row__cell">
                            <a class="text-amber-700" href="{{ route('palimpsestTemplateItems.edit', $palimpsestTemplateItem->id) }}" title="Edit {{ $palimpsestTemplateItem->title }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                        </div>
                        <div class="table_list__row__cell">
                            <form method="POST" action="{{ route('palimpsestTemplateItems.destroy', $palimpsestTemplateItem->id) }}">
                                @csrf
                                {{ method_field('DELETE') }}
                                <button data-delete-message="Stai per cancellare la voce {{ $palimpsestTemplateItem->title }}. Sei sicuro?" class="delete-link text-amber-700" type="submit" title="Trash {{ $palimpsestTemplateItem->title }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @endif
                </li>
            @endforeach
        </ol>
    </div>
    @endforeach
</div>

</div>

</x-layouts.panel_layout>

