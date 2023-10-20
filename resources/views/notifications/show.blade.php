<x-layouts.panel_layout>

<div class="body_section">

<a href="{{ route('notifications.index') }}" title="Notification list">&lsaquo; Back to all notifications</a>

@if ($notification->status != App\Enums\NotificationStatus::SENT->value)
<div class="pr-4 flex gap-4 items-end place-content-end relative w-full">

    <a href="{{ route("notifications.edit", $notification->id) }}" class="btn_new_entity text-center inline-flex items-center" >Modifica</a>

    <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST">
        @csrf
        {{ method_field('DELETE') }}
        <button data-delete-message="Stai per cancellare la notifica {{ $notification->title }}. Sei sicuro?" class="delete-link delete_btn" type="submit">Elimina</button>
    </form>
</div>
@endif

<h1 class="font-bold text-2xl py-8">{{ $notification->title }}</h1>

<div class="md:flex">
    <div class="md:basis-2/3 px-8">

        <div class="w-full mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                Message
            </label>
            <div>
                {{ $notification->message }}
            </div>
        </div>

    </div>

    <aside class="basis-1/3 border-l p-6">
        <div class="aside_info">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                Topic
            </label>
            <div>
                {{ $notification->topic }}
            </div>
        </div>
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                Type
            </label>
            {{ $notification->type }}
        </div>
        @if($notification->type == 'program' && $notification->program)
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                Program
            </label>
            <a href="{{ route('programs.show', $notification->program) }}">{{ $notification->program->title }}</a>
        </div>
        @endif
        @if($notification->type == 'season' && $notification->season)
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                Season
            </label>
            <a href="{{ route('seasons.show', $notification->season) }}">{{ $notification->season->title }}</a>
        </div>
        @endif
        @if($notification->type == 'episode' && $notification->episode)
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                Episode
            </label>
            <a href="{{ route('episodes.show', $notification->episode) }}">{{ $notification->episode->title }}</a>
        </div>
        @endif
        @if($notification->type == 'live' && $notification->live)
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                Live
            </label>
            <a href="{{ route('lives.show', $notification->live) }}">{{ $notification->live->title }}</a>
        </div>
        @endif
        @if($notification->type == 'news' && $notification->news)
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                News
            </label>
            <a href="{{ route('news.show', $notification->news) }}">{{ $notification->news->title }}</a>
        </div>
        @endif
        <div class="aside_info mt-8">
            <Status class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                Type
            </label>
            {{ $notification->status }}
        </div>

        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                Scheduled at
            </label>
            <div>
                <div class="flex gap-2">
                @if ($notification->scheduled_at > now())
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @endif
                {{ Carbon\Carbon::createFromDate($notification->scheduled_at)->format("d-m-Y H:i") }}
                </div>
            </div>
        </div>
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans("general.created at") }}
            </label>
            <div>
                {{ Carbon\Carbon::createFromDate($notification->created_at)->format("d-m-Y H:i") }}
            </div>
        </div>
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans("general.updated at") }}
            </label>
            <div>
                {{ Carbon\Carbon::createFromDate($notification->updated_at)->format("d-m-Y H:i") }}
            </div>
        </div>

    </aside>

</div>

</x-layouts.panel_layout>
