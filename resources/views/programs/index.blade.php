<x-layouts.panel_layout>

<div class="body_section">

<x-page_title>{{ trans('programs.Programs') }} ({{ $total }})</x-page_title>

<div class="pr-4 flex flex-col items-end relative">
    <div>
        <a href="{{ route('programs.create') }}" title="{{ trans('programs.New program') }}" class="btn_new_entity">{{ trans('programs.New program') }}</a>
    </div>

    <x-search.programs :request="$request"></x-search.programs>
</div>

@if (count($programs) === 0)
<div class="text-center my-12">{{ trans('programs.No program found') }}</div>
@else
<div class="default_table">
    <ol class="table_list">
    <li class="table_list__row">
        <div class="table_list__row__header_cell">
            {{ trans('general.title') }}
        </div>
        <div class="table_list__row__header_cell">
            {{ trans('general.status') }}
        </div>
        <div class="table_list__row__header_cell">
            {{ trans('programs.podcast') }}
        </div>
        <div class="table_list__row__header_cell_action">
            {{ trans('general.edit') }}
        </div>
        <div class="table_list__row__header_cell_action">
            {{ trans('general.delete') }}
        </div>
        </strong>
    </li>
    @foreach ($programs as $program)
    <li class="table_list__row hover:bg-slate-100">
        <div class="table_list__row__cell">
            <div class="flex gap-2">
                @if (!$program->canPublish())
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#600" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                @endif
                <a class="table_list__row__cell__title" href="{{ route('programs.show', $program->id) }}" title="{{ $program->title }}">{{ $program->title }}</a>
            </div>
        </div>
        <div class="table_list__row__cell">
            <x-badges.program_status :status="$program->status">{{ $program->status }}</x-badges.program_status>
        </div>
        <div class="table_list__row__cell">
           {{ trans('general.'.($program->podcast ? 'YES' : 'NO')) }}
        </div>
        <div class="table_list__row__cell">
            <a class="text-amber-700" href="{{ route('programs.edit', $program->id) }}" title="Edit {{ $program->title }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </a>
        </div>
        <div class="table_list__row__cell">
            <form method="POST" action="{{ route('programs.destroy', $program->id) }}">
                @csrf
                {{ method_field('DELETE') }}
                <button data-delete-message="Stai per cancellare il programma {{ $program->title }}. Sei sicuro?" class="delete-link text-amber-700" type="submit" title="Trash {{ $program->title }}">
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
{!! $programs->appends($request->query())->links() !!}
@endif
</div>

</x-layouts.panel_layout>

