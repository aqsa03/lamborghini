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

        <form id="model-form" class="p-12" action="{{ $formType == 'create' ? route('models.store') : route('models.update', $model->id) }}" method="POST" enctype="multipart/form-data">
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
            <input type="hidden" id="meride_video_id" name="meride_video_id" value="{{ $model->pre_existing_video_id ?? '' }}" />

            <input type="hidden" id="status" name="status" value="{{$model->status ?? 'DRAFT'}}" />
            <div class="w-full md:w-1/2 px-3 mt-12">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="category_id">
                    {{ trans("general.model") }}
                </label>
                <div class="inline-block relative w-64">
                    <select name="parent_id" class="form_select">
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
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="is_360">
                    {{ trans("videos.type") }}
                </label>
                <select name="type" class="form_select" required id="type">
                    <option value=" ">{{trans("general.select_video_type")}}</option>
                    <option value="PRE_EXISTING" {{ old("type", $video->type ?? '0') == 'PRE_EXISTING' ? 'selected' : '' }}>Pre-existing</option>
                </select>
            </div>
            <div class="w-full px-3 mt-12" id="pre-existing" style="display:none";>
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="pre_existing_video_id">
                    {{ trans("videos.pre-existing videos") }}
                </label>
                <div id="filtered_results" class="mt-2">
                    <input list="pre_existing_videos" name="pre_existing_video_id" class="form_select" id="pre_existing_video_id">
                    <datalist id="pre_existing_videos">
                        @foreach($meridePreExisting as $result)
                        <option value="{{ $result->id }}-{{ $result->title }}" data-id="{{$result->id}}" data-url="{{ $result->url_video_mp4 }}">
                            @endforeach
                    </datalist>
                </div>
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
                <input type="file" name="qr_code" accept="image/png, image/jpeg, image/webp" />
                <p><i>{{ trans("general.peso massimo immagine") }} {{ ini_get('upload_max_filesize') }}</i></p>
            </div>
            @if(empty($model->pre_existing_video_id))
            <div class="w-full md:w-1/2 px-3 mt-12" id="video" style="display:block;">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                    {{ trans("general.preview video") }}
                </label>

                @include('videos.render', ['entity' => $model ?? null, 'preview' => true])

                <div id="drag-drop-area-preview"></div>

            </div>
            @endif
            <div class="w-full px-3 mt-12">
                <div class="basis-1/2">
                    @if (!isset($model) or $model?->status != App\Enums\ModelStatus::PUBLISHED->value)
                    <button id="save-button" class="btn_save" type="submit">{{ trans("general.Save draft") }}</button>
                    @endif
                </div>
                @if (isset($model) and $model->canPublish() and (!$video->isPublished()))
                <div class="basis-1/2 text-right">
                    <button id="publish-button" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" type="submit">{{ trans("general.Publish") }}</button>
                </div>
                @endif
            </div>

        </form>
    </div>
    @push("scripts")
    @vite(['resources/js/models/form.js'])
    @endpush
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var preExistingVideoId = document.getElementById('meride_video_id').value;
            var preExistingSelect = document.getElementById("pre_existing_video_id");
            var options = document.querySelectorAll("#pre_existing_videos option");
            var selectedOption = Array.from(options).find(function(option) {
                return option.getAttribute("data-id") === preExistingVideoId;
            });

            if (selectedOption) {
                preExistingSelect.value = selectedOption.value;
                preExisting.style.display = 'block';
            } else {
                console.log("No matching option found");
                preExisting.style.display = 'none';
            }
        });
        const istypeSelect = document.getElementById('type');
        const preExisting = document.getElementById('pre-existing');
        const video = document.getElementById("video");
        if (istypeSelect.value === 'PRE_EXISTING') {
            preExisting.style.display = 'block';
            video.style.display = "none";
        } else {
            preExisting.style.display = 'none';
            video.style.display = "block";
        }
        istypeSelect.addEventListener('change', function() {
            if (istypeSelect.value === 'PRE_EXISTING') {

                preExisting.style.display = 'block';

                video.style.display = 'none';
            } else {
                preExisting.style.display = 'none';
                video.style.display = 'block';

            }
        });
        const programEditForm = document.getElementById("model-edit-form");
        const publishButton = document.getElementById("publish-button");
        publishButton.addEventListener("click", ev => {
            ev.preventDefault();
            document.getElementById("status").value = "PUBLISHED";
            programEditForm.submit();
            return false;
        });
    </script>
</x-layouts.panel_layout>