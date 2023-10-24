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

    <form  id="model-form" class="p-12" action="{{ $formType == 'create' ? route('models.store') : route('models.update', $model->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        {{ $formType == 'edit' ? method_field('PUT') : '' }}

        <input type="hidden" id="pay_per_view" name="pay_per_view" value="{{$model->pay_per_view ?? 0}}" />
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
        <div class="w-full md:w-1/2 px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="category_id">
                {{ trans("general.model") }}
            </label>
            <div class="inline-block relative w-64">
                <select name="parent_id" class="form_select" >
                <option disbaled value="null">{{ trans("general.choose model") }}</option>
                    @foreach ($models as $md)
                    <option {{ old("model_id", $model->parent_id ?? '') == $md->id ? 'selected' : '' }} value="{{ $md->id }}">{{ $md->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="title">
                {{ trans("general.title") }}
            </label>
            <input class="form_input" type="text" name="title" placeholder="{{ trans("general.title") }}" value="{{ old("title", $model->title ?? '') }}" required maxlength="255" />
        </div>
        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="description">
                {{ trans("general.description") }}
            </label>
            <textarea rows="3" name="description" placeholder="{{ trans("general.description") }}" class="form_input">{{ old("description", $model->description ?? '') }}</textarea>
        </div>    
        <div class="w-full md:w-1/2 px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans("general.poster image") }}
            </label>
            @if (isset($model) && !empty($model->imagePoster))
                <img src="{{ $model->imagePoster->url }}" title="{{ $model->imagePoster->name }}" />
            @endif
            <input type="file" name="image_poster" accept="image/png, image/jpeg, image/webp" />
            <p><i>{{ trans("general.peso massimo immagine") }} {{ ini_get('upload_max_filesize') }}</i></p>
        </div>

        <div class="w-full md:w-1/2 px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans("general.QR Scan") }}
            </label>
            @if (isset($model) && !empty($model->QRScan))
                <img src="{{ $model->QRScan->url }}" title="{{ $model->QRScan->name }}" />
            @endif
            <input type="file" name="qr_scan" accept="image/png, image/jpeg, image/webp" />
            <p><i>{{ trans("general.peso massimo immagine") }} {{ ini_get('upload_max_filesize') }}</i></p>
        </div>

        <div class="w-full md:w-1/2 px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans("general.preview video") }}
            </label>

            @include('videos.render', ['entity' => $model ?? null, 'preview' => true])

            <div id="drag-drop-area-preview"></div>

        </div>

        <div class="w-full md:w-1/2 px-3 mt-12">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans("general.main video") }}
            </label>

            @include('videos.render', ['entity' => $model ?? null, 'preview' => false])

            <div id="drag-drop-area"></div>

        </div>

        <div class="w-full px-3 mt-12">
            <div class="flex w-full">
                <button id="save-button" class="btn_save" type="submit">{{ trans("general.save") }}</button>
            </div>
        </div>

    </form>
</div>
@push("scripts")
@vite(['resources/js/models/form.js'])
@endpush
<script>
    // {{-- const programEditForm = document.getElementById("program-edit-form");
    // const publishButton = document.getElementById("publish-button");
    // publishButton.addEventListener("click", ev => {
    //     ev.preventDefault();
    //     document.getElementById("status").value = "PUBLISHED";
    //     programEditForm.submit();
    //     return false;
    // }); --}}
</script>
</x-layouts.panel_layout>
