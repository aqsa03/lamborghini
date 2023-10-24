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

    <form  id="episode-form" class="p-12" action="{{ $formType == 'create' ? route('videos.store') : route('videos.update', $video->id) }}" method="POST" enctype="multipart/form-data">
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
        
        <input type="hidden" id="status" name="status" value="{{$epivideosode->status ?? 'DRAFT'}}" />

        <div class="w-full md:w-1/2 px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="season_id">
                {{ trans("model.Model") }}
            </label>
            <div class="inline-block relative w-64">
                <select id="model_id" name="model_id" class="form_select" required onchange="{{ $formType == 'create' ? 'nextEpisodeNumber()' : '' }}" >
                    @foreach ($models as $model)
                    <option {{ old("model_id", $video->model_id ?? '') == $model->id ? 'selected' : '' }} value="{{ $model->id }}">{{ $model->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="title">
                {{ trans("general.title") }}
            </label>
            <input class="form_input" type="text" name="title" placeholder="{{ trans("general.title") }}" value="{{ old("title", $video->title ?? '') }}" required maxlength="255" />
        </div>
        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="short_description">
                {{ trans("general.description") }}
            </label>
            <textarea rows="3" name="description" placeholder="{{ trans("general.description") }}" class="form_input">{{ old("description", $video->description ?? '') }}</textarea>
        </div>
        <div class="w-full md:w-1/2 px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans("general.preview video") }}
            </label>

            @include('videos.render', ['entity' => $video ?? null, 'preview' => true])

            <div id="drag-drop-area-preview"></div>

        </div>

        <div class="w-full md:w-1/2 px-3 mt-12">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans("general.main video") }}
            </label>

            @include('videos.render', ['entity' => $video ?? null, 'preview' => false])

            <div id="drag-drop-area"></div>

        </div>

        <div class="w-full px-3 mt-12">
            <div class="basis-1/2">
                @if (!isset($video) or $video?->status != App\Enums\VideosStatus::PUBLISHED->value)
                <button id="save-button" class="btn_save" type="submit">{{ trans("general.Save draft") }}</button>
                @endif
            </div>
            @if (isset($video) and $video->canPublish())
            <div class="basis-1/2 text-right">
                <button id="publish-button" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" type="submit">{{ trans("general.Publish") }}</button>
            </div>
            @endif
        </div>

    </form>
</div>

@push("scripts")
@vite(['resources/js/videos/form.js'])
@endpush

<script>
    {{-- const createForm = document.getElementById("episode-form");
    const publishButton = document.getElementById("publish-button");
    publishButton.addEventListener("click", ev => {
        ev.preventDefault();
        document.getElementById("status").value = "PUBLISHED";
        createForm.submit();
        return false;
    }); --}}
</script>
</x-layouts.panel_layout>
