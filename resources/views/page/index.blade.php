<x-layouts.panel_layout>

<div class="body_section">

<x-page_title>{{ trans('pages.Pages') }}</x-page_title>

<div class="default_table">
    <ol class="table_list">
    <li class="table_list__row">
        <div class="table_list__row__header_cell">
            {{ trans('general.title') }}
        </div>
        <div class="table_list__row__header_cell_action">
            {{ trans('general.edit') }}
        </div>
        </strong>
    </li>
@foreach ($pages as $page)
    <li class="table_list__row hover:bg-slate-100">
        <div class="table_list__row__cell">
            {{ $page->title }}
        </div>
        <div class="table_list__row__cell">
            <a class="text-amber-700" href="{{ route('pages.edit', $page->id) }}" title="Edit {{ $page->title }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </a>
        </div>
    </li>
    @endforeach
    </ol>
</div>

</div>

</x-layouts.panel_layout>

