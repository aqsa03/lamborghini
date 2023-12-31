<x-layouts.panel_layout>

<div class="body_section">

<x-page_title>{{ trans('general.model') }} ({{ $total }})</x-page_title>

<div class="text-right pr-4">
    <a href="{{ route('models.create') }}" title="Crea una nuova modella" class="btn_new_entity">Nuova modella</a>
</div>

<div class="default_table">
    <ol class="table_list">
    <li class="table_list__row">
        <div class="table_list__row__header_cell">
            {{ trans('general.parent') }}
        </div>
        <div class="table_list__row__header_cell">
            {{ trans('general.title') }}
        </div>
        <div class="table_list__row__header_cell">
            {{ trans('general.description') }}
        </div>
        <div class="table_list__row__header_cell">
            {{ trans('general.status') }}
        </div>
        <div class="table_list__row__header_cell_action">
            {{ trans('general.edit') }}
        </div>
        <div class="table_list__row__header_cell_action">
            {{ trans('general.delete') }}
        </div>
        </strong>
    </li>
@foreach ($models as $model)
    <li class="table_list__row hover:bg-slate-100">
    <div class="table_list__row__cell">
    {{  $model->parentCategory->title ?? ''  }}
        </div>
        <div class="table_list__row__cell">
            <div class="flex gap-2">
                @if ($model->type!= App\Enums\VideoType::EXT_VIEW->value and $model->type!= App\Enums\VideoType::PRE_EXISTING->value )
                @if( !$model->canPublish())
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#600" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                @endif
                @endif
                {{ $model->title }}
            </div>
        </div>
        <div class="table_list__row__cell">
            {{ $model->description }}
        </div>
       
        <div class="table_list__row__cell">
            {{ $model->status }}
        </div>
        <div class="table_list__row__cell">
            <a class="text-amber-700" href="{{ route('models.edit', $model->id) }}" title="Edit {{ $model->title }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </a>
        </div>
        <div class="table_list__row__cell">
            <form method="POST" action="{{ route('models.destroy', $model->id) }}">
                @csrf
                {{ method_field('DELETE') }}
                <button data-delete-message="Stai per cancellare la categoria {{ $model->title }}. Sei sicuro?" class="delete-link text-amber-700" type="submit" title="Trash {{ $model->title }}">
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

{!! $models->links() !!}

</div>

</x-layouts.panel_layout>

