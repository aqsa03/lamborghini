<x-layouts.panel_layout>

<div class="body_section">

    <x-form_errors :errors="$errors"></x-form_errors>

    <form  id="palimpsestItem-form" class="p-12" action="{{ $formType == 'create' ? route('palimpsestItems.store') : route('palimpsestItems.update', $palimpsestItem->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        {{ $formType == 'edit' ? method_field('PUT') : '' }}

        <div class="w-full md:w-1/2 px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="live_id">
                {{ trans("lives.Live") }}
            </label>
            <div class="inline-block relative w-64">
                <input type="hidden" name="live_id" value="{{old("live_id", $palimpsestItem->live_id ?? $live->id)}}" />
                {{ isset($palimpsestItem->live_id) ? $palimpsestItem->live->title : $live->title }}
            </div>
        </div>

        <div class="w-full md:w-1/2 px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="program_id">
                {{ trans("programs.Program") }}
            </label>
            <div class="inline-block relative w-64">
                <select name="program_id" class="form_select" >
                    <option {{ old("program_id", $palimpsestItem->program_id ?? '') == '' ? 'selected' : '' }} value="">&lt; {{ trans("programs.Program") }} &gt;</option>
                    @foreach ($programs as $program)
                        <option {{ old("program_id", $palimpsestItem->program_id ?? '') == $program->id ? 'selected' : '' }} value="{{ $program->id }}">{{ $program->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="title">
                {{ trans("general.title") }}
            </label>
            <input class="form_input" type="text" name="title" placeholder="{{ trans("general.title") }}" value="{{ old("title", $palimpsestItem->title ?? '') }}" required maxlength="255" />
        </div>

        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="start_at">
                {{ trans("general.start at") }}
            </label>
            <input class="form_input" type="datetime-local" name="start_at" placeholder="{{ trans("general.start at") }}" value="{{ old("start_at", $palimpsestItem->start_at ?? now()) }}" required " />
        </div>

        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="end_at">
                {{ trans("general.end at") }}
            </label>
            <input class="form_input" type="datetime-local" name="end_at" placeholder="{{ trans("general.end at") }}" value="{{ old("end_at", $palimpsestItem->end_at ?? now()) }}" required " />
        </div>

        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="description">
                {{ trans("general.description") }}
            </label>
            <textarea rows="3" name="description" placeholder="{{ trans("general.description") }}" class="form_input">{{ old("description", $palimpsestItem->description ?? '') }}</textarea>
        </div>
        
        <div class="w-full md:w-1/2 px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans("general.image") }}
            </label>
            @if (isset($palimpsestItem) && !empty($palimpsestItem->image))
                <img src="{{ $palimpsestItem->image->url }}" title="{{ $palimpsestItem->image->name }}" />
            @endif
            <input type="file" name="image" accept="image/png, image/jpeg, image/webp" />
            <p><i>{{ trans("general.peso massimo immagine") }} {{ ini_get('upload_max_filesize') }}</i></p>
        </div>

        <div class="w-full px-3 mt-12">
            <div class="flex w-full">
                <button id="save-button" class="btn_save" type="submit">{{ trans("general.save") }}</button>
            </div>
        </div>

    </form>
</div>

</x-layouts.panel_layout>