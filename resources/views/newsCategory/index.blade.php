<x-layouts.panel_layout>

<div class="body_section">

<x-page_title>{{ trans('newsCategories.NewsCategories') }} ({{ $total }})</x-page_title>

<div class="text-right pr-4">
    <a href="{{ route('newsCategories.create') }}" title="Crea una nuova categoria" class="btn_new_entity">{{ trans('newsCategories.New newsCategory') }}</a>
</div>

<div class="default_table">
    <ol class="table_list">
    <li class="table_list__row">
        <div class="table_list__row__header_cell">
            {{ trans('general.title') }}
        </div>
        <div class="table_list__row__header_cell_action">
            {{ trans('general.edit') }}
        </div>
        <div class="table_list__row__header_cell_action">
            {{ trans('general.delete') }}
        </div>
        </strong>
    </li>
@foreach ($newsCategories as $newsCategory)
    <li class="table_list__row hover:bg-slate-100">
        <div class="table_list__row__cell">
            <a class="table_list__row__cell__title" href="{{ route('newsCategories.show', $newsCategory->id) }}" title="{{ $newsCategory->title }}">{{ $newsCategory->title }}</a>
        </div>
        <div class="table_list__row__cell">
            <a class="text-amber-700" href="{{ route('newsCategories.edit', $newsCategory->id) }}" title="Edit {{ $newsCategory->title }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </a>
        </div>
        <div class="table_list__row__cell">
            <form method="POST" action="{{ route('newsCategories.destroy', $newsCategory->id) }}">
                @csrf
                {{ method_field('DELETE') }}
                <button data-delete-message="Stai per cancellare la categoria {{ $newsCategory->title }}. Sei sicuro?" class="delete-link text-amber-700" type="submit" title="Trash {{ $newsCategory->title }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </form>
        </div>
    </li>
    @endforeach
    </ol>
</div>

{!! $newsCategories->links() !!}

</div>

</x-layouts.panel_layout>

