<x-layouts.panel_layout>

<div class="body_section">

<x-page_title>{{ trans('notifications.notifications') }} ({{ $total }})</x-page_title>

<div class="text-right pr-4">
    <a href="{{ route('notifications.create') }}" title="Crea una nuova notifica" class="btn_new_entity">{{ trans('notifications.New notification') }}</a>
</div>

@if (count($notifications) === 0)
<div class="text-center my-12">{{ trans('notifications.No notification found') }}</div>
@else
<div class="default_table">
    <ol class="table_list">
    <li class="table_list__row">
        <div class="table_list__row__header_cell">
            {{ trans('general.title') }}
        </div>
        <div class="table_list__row__header_cell">
            {{ trans('general.type') }}
        </div>
        <div class="table_list__row__header_cell">
            {{ trans('general.status') }}
        </div>
        <div class="table_list__row__header_cell">
            {{ trans('notifications.scheduling') }}
        </div>
        <div class="table_list__row__header_cell_action">
            {{ trans('general.edit') }}
        </div>
        <div class="table_list__row__header_cell_action">
            {{ trans('general.delete') }}
        </div>
        </strong>
    </li>
@foreach ($notifications as $notification)
    <li class="table_list__row hover:bg-slate-100">
        <div class="table_list__row__cell">
            <a class="table_list__row__cell__title" href="{{ route('notifications.show', $notification->id) }}" title="{{ $notification->title }}">{{ $notification->title }}</a>
        </div>
        <div class="table_list__row__cell">
            {{ trans('general.'.strtolower($notification->type)) }}
        </div>
        <div class="table_list__row__cell">
            {{ trans('general.'.strtolower($notification->status)) }}
        </div>
        <div class="table_list__row__cell">
            <div class="flex gap-2">
                @if ($notification->scheduled_at > now())
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>

                @endif
                {{ Carbon\Carbon::createFromDate($notification->scheduled_at)->format("d-m-Y H:i") }}
            </div>
        </div>
        @if ($notification->status != App\Enums\NotificationStatus::SENT->value)
        <div class="table_list__row__cell">
            <a class="text-amber-700" href="{{ route('notifications.edit', $notification->id) }}" title="Edit {{ $notification->title }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </a>
        </div>
        @else
        <div class="table_list__row__cell">&nbsp;</div>
        @endif
        <div class="table_list__row__cell">
            <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}">
                @csrf
                {{ method_field('DELETE') }}
                <button data-delete-message="Stai per cancellare la notifica {{ $notification->title }}. Sei sicuro?" class="delete-link text-amber-700" type="submit" title="Trash {{ $notification->title }}">
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
{!! $notifications->links() !!}
@endif
</div>

</x-layouts.panel_layout>

