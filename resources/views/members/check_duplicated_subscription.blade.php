<x-layouts.panel_layout>

<div class="body_section">

<x-page_title>Probabili utenti con sottoscrizioni duplicate</x-page_title>

@if (count($customers) === 0)
<div class="text-center my-12">Nessun membro trovato</div>
@else
<div class="default_table">
    <ol class="table_list">
    <li class="table_list__row">
        <div class="table_list__row__header_cell">
            Email
        </div>
        <div class="table_list__row__header_cell">
            Stripe Customer ID
        </div>
        <div class="table_list__row__header_cell">
            Firebase UID
        </div>
    </li>
    @foreach ($customers as $customer)
    <li class="table_list__row hover:bg-slate-100">
        <div class="table_list__row__cell">
            {{ $customer->email }}
        </div>
        <div class="table_list__row__cell">
            <a target="_blank" href="{{ $customer->livemode ? 'https://dashboard.stripe.com/customers/'.$customer->id : 'https://dashboard.stripe.com/test/customers/'.$customer->id }}">{{ $customer->id }}</a>
        </div>
        <div class="table_list__row__cell">
            @if(isset($customer->metadata->firebaseUID))
                <a target="_blank" href="{{ route('members.show', $customer->metadata->firebaseUID) }}">{{ $customer->metadata->firebaseUID }}</a>
            @endif
        </div>
    </li>
    @endforeach
    </ol>
</div>
@endif
</div>

</x-layouts.panel_layout>

