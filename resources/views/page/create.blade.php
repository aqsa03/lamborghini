<x-layouts.panel_layout>

    <div class="body_section">

        <x-form_errors :errors="$errors"></x-form_errors>
        <form class="default_form" action="{{ $formType == 'create' ? route('pages.store') : route('page.update', $page->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            {{ $formType == 'edit' ? method_field('PUT') : '' }}
            <div class="w-full px-3 mt-12">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="title">
                    {{ trans('general.title') }}
                </label>
                <input value="{{ old('title', $page?->title) }}" class="form_input" type="text" name="title" placeholder="{{ trans('general.title') }}" required />
            </div>
            <div class="w-full px-3 mt-12">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="description">
                    {{ trans("general.description") }}
                </label>
                <textarea rows="3" name="description" placeholder="{{ trans("general.description") }}" class="form_input">{{ old("description", $page->description ?? '') }}</textarea>
            </div>
            <div class="w-full px-3 mt-12">
                <button class="btn_save" type="submit">{{ trans("general.save") }}</button>
            </div>
        </form>

    </div>

</x-layouts.panel_layout>