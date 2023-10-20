@props(['request'])
<?php
$searchQueryIsEmpty = empty($request->except("page"));
$queryHasOnlyPage = (count($request->query()) === 1 AND !empty($request->query('page')));
$searchOpen = !($searchQueryIsEmpty OR $queryHasOnlyPage);
?>
<div class="mt-12 flex flex-col w-full" x-data="{ searchOpen: {{ $searchOpen === true ? 'true' : 'false' }} }">
    <div class="text-right">
        <button @click="searchOpen = !searchOpen">
            <svg x-cloak x-show="!searchOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
            <svg x-cloak x-show="searchOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </button>
    </div>
    <div x-cloak x-show="searchOpen" class="w-full border-t">
        <form method="GET">
            <div class="md:flex w-full">
                <div class="w-full px-3 mt-12">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="type">
                        {{ trans('programs.Program') }}
                    </label>
                    <select name="program_id" class="form_select">
                        <option value="">{{ trans('search.Select').' '.trans('programs.Program') }}</option>
                        @foreach (App\Models\Program::all() as $p)
                        <option value="{{ $p->id }}"{{ $request->query('program_id', '') == $p->id ? ' selected="selected"' : '' }}>{{ $p->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="md:flex w-full">
                <div class="w-full px-3 my-12">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="title">
                        {{ trans('general.title') }}
                    </label>
                    <input type="text" min-length="3" max-length="200" class="form_input" name="title" value="{{ $request->query('title', '') }}" />
                </div>
                <div class="w-full px-3 mt-12">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="type">
                        {{ trans('general.status') }}
                    </label>
                    <select name="status" class="form_select">
                        <option value="-1">{{ trans('search.Select status') }}</option>
                        @foreach (App\Enums\SeasonStatus::cases() as $seasonStatus)
                        <option value="{{ $seasonStatus->value }}"{{ $request->query('status', '') == $seasonStatus->value ? ' selected="selected"' : '' }}>{{ ucfirst(trans("general.".strtolower($seasonStatus->value))) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="w-full px-3 border-t border-b text-right">
                <button type="submit" class="btn_new_entity text-center inline-flex items-center my-6">
                    {{ trans('search.Search') }}&nbsp;
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>
