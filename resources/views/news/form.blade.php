@push('headstyles')

<link href="https://transloadit.edgly.net/releases/uppy/v1.5.0/uppy.min.css" rel="stylesheet">

@endpush


@push('headscripts')

<script src="https://transloadit.edgly.net/releases/uppy/v1.5.0/uppy.min.js"></script>

@endpush

<x-layouts.panel_layout>

<div class="body_section">

    <x-form_errors :errors="$errors"></x-form_errors>

    <input type="hidden" id="tus_token" value="{{ $tusToken }}" />
    <input type="hidden" id="storage_upload_endpoint" value="{{ $storageUploadEndpoint }}" />

    <form  id="news-form" class="p-12" action="{{ $formType == 'create' ? route('news.store') : route('news.update', $news->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        {{ $formType == 'edit' ? method_field('PUT') : '' }}

        <input type="hidden" id="video_name" name="video_name" value="" />
        <input type="hidden" id="video_upload_url" name="video_upload_url" value="" />
        <input type="hidden" id="video_width" name="video_width" value="" />
        <input type="hidden" id="video_height" name="video_height" value="" />
        <input type="hidden" id="video_duration" name="video_duration" value="" />

        <input type="hidden" id="video_preview_name" name="video_preview_name" value="" />
        <input type="hidden" id="video_preview_upload_url" name="video_preview_upload_url" value="" />
        <input type="hidden" id="video_preview_width" name="video_preview_width" value="" />
        <input type="hidden" id="video_preview_height" name="video_preview_height" value="" />
        <input type="hidden" id="video_preview_duration" name="video_preview_duration" value="" />
        
        <input type="hidden" id="status" name="status" value="{{$news->status ?? 'DRAFT'}}" />

        <div class="w-full md:w-1/2 px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="news_category_id">
                {{ trans("general.category") }}
            </label>
            <div class="inline-block relative w-64">
                <select name="news_category_id" class="form_select" required >
                    @foreach ($newsCategories as $category)
                    <option {{ old("news_category_id", $news->news_category_id ?? '') == $category->id ? 'selected' : '' }} value="{{ $category->id }}">{{ $category->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="title">
                {{ trans("general.title") }}
            </label>
            <input class="form_input" type="text" name="title" placeholder="{{ trans("general.title") }}" value="{{ old("title", $news->title ?? '') }}" required maxlength="255" />
        </div>

        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="description">
                {{ trans("general.content") }}
            </label>

            <div id="description" class="editor_textarea"></div>
            <input type="hidden" name="description">
            <script type="text/plain" id="news_source_description">{!! old("description", $news->description ?? '') !!}</script>
        </div>

        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="short_description">
                {{ trans("general.short_description") }}
            </label>
            <textarea rows="3" name="short_description" placeholder="{{ trans("general.short_description") }}" class="form_input">{{ old("short_description", $news->short_description ?? '') }}</textarea>
        </div>

        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="tags">
                {{ trans("general.tags") }}
            </label>
            <input class="form_input" value="{{ old("tags", (isset($news) && !empty($news->tags)) ? implode(', ', $news->tags) : '') }}" type="text" name="tags" placeholder="{{ trans("general.comma separated tags") }}" />
        </div>

        
        <div class="w-full md:w-1/2 px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans("general.image") }}
            </label>
            @if (isset($news) && !empty($news->image))
                <img src="{{ $news->image->url }}" title="{{ $news->image->name }}" />
            @endif
            <input type="file" name="image" accept="image/png, image/jpeg, image/webp" />
            <p><i>{{ trans("general.peso massimo immagine") }} {{ ini_get('upload_max_filesize') }}</i></p>
        </div>

        <div class="w-full md:w-1/2 px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans("general.preview video") }}
            </label>

            @include('videos.render', ['entity' => $news ?? null, 'preview' => true])

            <div id="drag-drop-area-preview"></div>

        </div>

        <div class="w-full md:w-1/2 px-3 mt-12">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans("general.main video") }}
            </label>

            @include('videos.render', ['entity' => $news ?? null, 'preview' => false])

            <div id="drag-drop-area"></div>

        </div>

        <div class="w-full px-3 mt-12">
            <div class="basis-1/2">
                @if (!isset($news) or $news?->status != App\Enums\NewsStatus::PUBLISHED->value)
                <button id="save-button" class="btn_save" type="submit">{{ trans("general.Save draft") }}</button>
                @endif
            </div>
            <div class="basis-1/2 text-right">
                <button id="publish-button" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" type="submit">{{ trans("general.Publish") }}</button>
            </div>
        </div>

    </form>
</div>

@push("scripts")
@vite(['resources/js/news/form.js'])
@endpush

<script>
    {{-- const createForm = document.getElementById("news-form");
    const publishButton = document.getElementById("publish-button");
    publishButton.addEventListener("click", ev => {
        ev.preventDefault();
        document.getElementById("status").value = "PUBLISHED";
        createForm.submit();
        return false;
    }); --}}
</script>

</x-layouts.panel_layout>
