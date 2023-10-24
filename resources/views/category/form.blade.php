<x-layouts.panel_layout>

<div class="body_section">

    <x-form_errors :errors="$errors"></x-form_errors>
    <form class="default_form" action="{{ $formType == 'create' ? route('categories.store') : route('categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        {{ $formType == 'edit' ? method_field('PUT') : '' }}
        <div class="w-full md:w-1/2 px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="category_id">
                {{ trans("general.parent category") }}
            </label>
        </div>
        <div class="inline-block relative w-64">
                <select name="parent_id" class="form_select">
                    <option disbaled value="null">{{ trans("general.choose category") }}</option>
                    @foreach ($categories as $catgry)
                    <option {{ old("category_id", $category->category_id ?? '') == $catgry->id ? 'selected' : '' }} value="{{ $catgry->id}}">{{ $catgry->title }}</option>
                    @endforeach
                </select>
            </div>
        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="title">
                {{ trans('general.title') }}
            </label>
            <input value="{{ old('title', $category?->title) }}" class="form_input" type="text" name="title" placeholder="{{ trans('general.title') }}" required />
        </div>
        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="description">
                {{ trans("general.description") }}
            </label>
            <textarea rows="3" name="description" placeholder="{{ trans("general.description") }}" class="form_input">{{ old("description", $category->description ?? '') }}</textarea>
        </div>
        <div class="w-full md:w-1/2 px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans("general.image") }}
            </label>
            @if (isset($category) && !empty($category->image))
                <img src="{{ $category->image->url }}" title="{{ $category->image->name }}" />
            @endif
            <input type="file" name="image" accept="image/png, image/jpeg, image/webp" />
            <p><i>{{ trans("general.peso massimo immagine") }} {{ ini_get('upload_max_filesize') }}</i></p>
        </div>

        <div class="w-full px-3 mt-12">
            <button class="btn_save" type="submit">{{ trans("general.save") }}</button>
        </div>
    </form>

</div>

</x-layouts.panel_layout>
