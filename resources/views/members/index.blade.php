<x-layouts.panel_layout>

<div class="body_section">

<x-page_title>Membri</x-page_title>

<div class="pr-4 flex flex-col items-end relative">
    <x-search.members :request="$request"></x-search.members>
</div>

@if (count($members) === 0)
<div class="text-center my-12">Nessun membro trovato</div>
@else
<div class="default_table">
    <ol class="table_list">
    <li class="table_list__row">
        <div class="table_list__row__header_cell">
            {{ trans("general.E-mail") }}
        </div>
        <div class="table_list__row__header_cell">
            {{ trans("general.E-mail verified") }}
        </div>
        <div class="table_list__row__header_cell">
            {{ trans_choice("general.role", 2) }}
        </div>
        <div class="table_list__row__header_cell">
            {{ trans("general.created at") }}
        </div>
        @if (Auth::user()->is_root())
        <div class="table_list__row__header_cell_action">
            {{ trans('general.edit') }}
        </div>
        <div class="table_list__row__header_cell_action">
            {{ trans('general.delete') }}
        </div>
        </strong>
        @endif
    </li>
    @foreach ($members as $member)
    <li class="table_list__row hover:bg-slate-100">
        <div class="table_list__row__cell">
            <a class="table_list__row__cell__title {{ $member->disabled ? 'line-through' : '' }}" href="{{ route('members.show', $member->uid) }}" title="{{ $member->email }}">{{ $member->email }}</a>
        </div>
        <div class="table_list__row__cell">
            @if($member->emailVerified)
                <img src="{{ asset('assets/imgs/ok.svg') }}" class="w-4 h-4" />
            @endif
        </div>
        <div class="table_list__row__cell">
            <ul>
            @foreach ($member->customClaims as $key => $value)
                <li><strong>{{ $key }}</strong>: {{ $value }}</li>
            @endforeach
            </ul>
        </div>
        <div class="table_list__row__cell">
            {{ $member->metadata->createdAt->format("d-m-Y H:i")}}
        </div>

        @if (Auth::user()->is_root())
        <div class="table_list__row__cell">
            <a class="text-amber-700" href="{{ route('members.edit', $member->uid) }}" title="Edit {{ $member->email }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </a>
        </div>
        <div class="table_list__row__cell">
            <form method="POST" action="{{ route('members.destroy', $member->uid) }}">
                @csrf
                {{ method_field('DELETE') }}
                <button data-delete-message="Stai per cancellare il membro {{ $member->email }}. Eliminandolo l'utente non sarà più in grado di loggarsi alle App, i suoi dati su Firebase e Stripe verranno cancellati. Sei sicuro?" class="delete-link text-amber-700" type="submit" title="Trash {{ $member->email }}">
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
@endif

{!! $paginator->links() !!}
</div>

</x-layouts.panel_layout>

