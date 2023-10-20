@props(['entity'])
<div class="w-full md:w-1/2 px-3 mt-12">
    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="type">
        {{ trans('general.type') }}
    </label>
    <div class="inline-block relative w-64">
        <select x-model="selectedType" name="type" class="form_select">
            @foreach (['program', 'season', 'episode', 'live', 'news'] as $type)
            <option {{ strtolower(old("type", $entity?->type)) == strtolower($type) ? 'selected' : '' }} value="{{ $type }}">{{ $type }}</option>
            @endforeach
        </select>
    </div>
</div>

<div x-cloak x-show="selectedType === 'program'" x-transition class="w-full md:w-1/2 px-3 mt-12">
    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="type">
        {{ trans("programs.Select the program") }}
    </label>
    @if (!empty($entity?->program_id))
    <x-search_string_component disabledInputHidden="selectedType !== 'program'"  entityType="programs" selectedEntityId="{{ $entity->program_id }}" selectedEntityString="{{ $entity->program?->search_string }}"></x-search_string_component>
    @else
    <x-search_string_component disabledInputHidden="selectedType !== 'program'"  entityType="programs"></x-search_string_component>
    @endif
</div>
<div x-cloak x-show="selectedType === 'season'" x-transition class="w-full md:w-1/2 px-3 mt-12">
    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="type">
        {{ trans("seasons.Select the season") }}
    </label>
    @if (!empty($entity?->season_id))
    <x-search_string_component disabledInputHidden="selectedType !== 'season'" entityType="seasons" selectedEntityId="{{ $entity->season_id }}" selectedEntityString="{{ $entity->season?->search_string }}"></x-search_string_component>
    @else
    <x-search_string_component disabledInputHidden="selectedType !== 'season'" entityType="seasons"></x-search_string_component>
    @endif
</div>
<div x-cloak x-show="selectedType === 'episode'" x-transition class="w-full md:w-1/2 px-3 mt-12">
    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="type">
        {{ trans("episodes.Select the episode") }}
    </label>
    @if (!empty($entity?->episode_id))
    <x-search_string_component disabledInputHidden="selectedType !== 'episode'" entityType="episodes" selectedEntityId="{{ $entity->episode_id }}" selectedEntityString="{{ $entity->episode?->search_string }}"></x-search_string_component>
    @else
    <x-search_string_component disabledInputHidden="selectedType !== 'episode'" entityType="episodes"></x-search_string_component>
    @endif
</div>
<div x-cloak x-show="selectedType === 'live'" x-transition class="w-full md:w-1/2 px-3 mt-12">
    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="type">
        {{ trans("lives.Select the live") }}
    </label>
    @if (!empty($entity?->live_id))
    <x-search_string_component disabledInputHidden="selectedType !== 'live'" entityType="lives" selectedEntityId="{{ $entity->live_id }}" selectedEntityString="{{ $entity->live?->search_string }}"></x-search_string_component>
    @else
    <x-search_string_component disabledInputHidden="selectedType !== 'live'" entityType="lives"></x-search_string_component>
    @endif
</div>
<div x-cloak x-show="selectedType === 'news'" x-transition class="w-full md:w-1/2 px-3 mt-12">
    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="type">
        {{ trans("news.Select the news") }}
    </label>
    @if (!empty($entity?->news_id))
    <x-search_string_component disabledInputHidden="selectedType !== 'news'" entityType="news" selectedEntityId="{{ $entity->news_id }}" selectedEntityString="{{ $entity->news?->search_string }}"></x-search_string_component>
    @else
    <x-search_string_component disabledInputHidden="selectedType !== 'news'" entityType="news"></x-search_string_component>
    @endif
</div>