<x-layouts.panel_layout>

<div class="body_section">

<a href="{{ route('members.index') }}" title="Úembers list">&lsaquo; Back to all members</a>

<div class="pr-4 flex gap-4 items-end place-content-end relative w-full">

    @if (Auth::user()->is_root())
    <a href="{{ route("members.edit", $member->uid) }}" class="btn_new_entity text-center inline-flex items-center" >Modifica</a>
    <form action="{{ route('members.destroy', $member->uid) }}" method="POST">
        @csrf
        {{ method_field('DELETE') }}
        <button data-delete-message="Stai per cancellare il membro {{ $member->email }}. Eliminandolo l'utente non sarà più in grado di loggarsi alle App, i suoi dati su Firebase e Stripe verranno cancellati. Sei sicuro?" class="delete-link delete_btn" type="submit">Elimina</button>
    </form>
    @endif
</div>

<h1 class="font-bold text-2xl py-8">{{ $member->email }}</h1>

<div class="md:flex">
    <div class="md:basis-2/3 px-8">
        <div class="aside_info">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                Display Name
            </label>
            {{ $member->displayName }}
        </div>
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                Uid
            </label>
            {{ $member->uid }}
        </div>
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans("general.Disabled") }}
            </label>
            {{ $member->disabled ? 'SI' : 'NO' }}
        </div>
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans("general.E-mail verified") }}
            </label>
            {{ $member->emailVerified ? 'SI' : 'NO' }}
        </div>
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans_choice("general.role", 2) }}
            </label>
            <ol class="list-decimal ml-8">
            @foreach ($member->customClaims as $key => $value)
                <li>{{ $key.' -> '.$value }}</li>
            @endforeach
            </ol>
        </div>
    </div>

    <aside class="basis-1/3 border-l p-6">
        <div class="aside_info">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans("general.created at") }}
            </label>
            {{ $member->metadata->createdAt?->format("d-m-Y H:i")}}
        </div>
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                Last Login At
            </label>
            {{ $member->metadata->lastLoginAt?->format("d-m-Y H:i")}}
        </div>
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                Password Updated At
            </label>
            {{ $member->metadata->passwordUpdatedAt?->format("d-m-Y H:i")}}
        </div>
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                Last Refresh At
            </label>
            {{ $member->metadata->lastRefreshAt?->format("d-m-Y H:i")}}
        </div>
        @if($stripe_customer)
            <div class="aside_info mt-8">
                <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                    Stripe Info
                </label>
                <ul>
                    <li>Customer ID: {{$stripe_customer->id}}</li>
                    <li>Name: <span id="stipe_name">{{$stripe_customer->name}}</span></li>
                </ul>
            </div>
        @endif
        @if($stripe_customer_payments and !empty($stripe_customer_payments))
            <div class="aside_info mt-8">
                <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                    Stripe Payments
                </label>
                <ul>
                    @foreach($stripe_customer_payments->data as $p)
                        <li class="{{$p->charges->data[0]->refunds->data ? 'line-through' : ''}}">{{Carbon\Carbon::parse($p->created)->format("d-m-Y H:i")}} - {{number_format($p->amount/100, 2, ',', '.')}} &euro; {{$p->charges->data[0]->refunds->data ? 'Rimborsato' : ''}}</li>
                    @endforeach
                    @if($stripe_customer_payments->has_more)
                        <li>...</li>
                    @endif
                </ul>
            </div>
        @endif
    </aside>
</div>

</x-layouts.panel_layout>
