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

        <form id="video-form" class="p-12" action="{{ $formType == 'create' ? route('videos.store') : route('videos.update', $video->id) }}" method="POST" enctype="multipart/form-data">
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
            <input type="hidden" id="meride_video_id" name="meride_video_id" value="{{ $video->pre_existing_video_id ?? '' }}" />
            <input type="hidden" id="status" name="status" value="{{$video->status ?? 'DRAFT'}}" />
            <div class="w-full md:w-1/2 px-3 mt-12">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="category_id">
                    {{ trans("general.category") }}
                </label>
                <div class="inline-block relative w-64">
                    <select id="category_id" name="category_id" class="form_select">
                    <option disbaled value="">{{ trans("general.choose category") }}</option>
                        @foreach ($categories as $category)
                        <option {{ old("category_id", $video->category_id ?? '') == $category->id ? 'selected' : '' }} value="{{ $category->id }}">{{ $category->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="w-full md:w-1/2 px-3 mt-12">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="model_id">
                    {{ trans("model.Model") }}
                </label>
                <div class="inline-block relative w-64">
                    <select id="model_id" name="model_id" class="form_select">
                    <option disbaled value="">{{ trans("general.choose model") }}</option>
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
            <div class="w-full px-3 mt-12">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="product_video">
                    {{ trans("videos.product video") }}
                </label>
                <select name="product_video" class="form_select" required>
                    <option {{ strtolower(old("product_video", $video->product_video ?? '')) == '0' ? 'selected' : '' }} value="0">No</option>
                    <option {{ strtolower(old("product_video", $video->product_video ?? '')) == '1' ? 'selected' : '' }} value="1">Si</option>
                </select>
            </div>
            <div class="w-full px-3 mt-12">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="subtitles">
                    {{ trans("videos.subtitles") }}
                </label>
                <select name="subtitles" class="form_select" required>
                    <option {{ strtolower(old("subtitles", $video->subtitles ?? '')) == '0' ? 'selected' : '' }} value="0">No</option>
                    <option {{ strtolower(old("subtitles", $video->subtitles ?? '')) == '1' ? 'selected' : '' }} value="1">Si</option>
                </select>
            </div>
            <div class="w-full px-3 mt-12">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="type">
                    {{ trans("videos.type") }}
                </label>
                <select name="type" class="form_select" id="type">
                    <option value=""  {{ old("type", $video->type ?? '') == '' ? 'selected' : '' }}>
                        {{ trans("general.select_video_type") }}
                    </option>
                    <option value="EXT_VIEW" {{ old("type", $video->type ?? '0') == 'EXT_VIEW' ? 'selected' : '' }}>ext_view</option>
                    <option value="PRE_EXISTING" {{ old("type", $video->type ?? '0') == 'PRE_EXISTING' ? 'selected' : '' }}>Pre-existing</option>
                </select>
            </div>
            <div class="w-full px-3 mt-12" id="ext_view_url_field" style="display:none;">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="ext_view_url">
                    {{ trans("videos.ext_view_url") }}
                </label>
                <input class="form_input" type="text" name="ext_view_url" id="ext_view_url" placeholder="{{ trans("videos.ext_view_url") }}" required value="{{ old("ext_view_url", $video->ext_view_url ?? '') }}" />
            </div>
            <div class="w-full px-3 mt-12" id="thumb_num_container" style="display:none;">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="thumb_num">
                    {{ trans("videos.thumb_num") }}
                </label>
                <input class="form_input" type="text" name="thumb_num" id="thumb_num" placeholder="{{ trans("videos.thumb_num") }}" required value="{{ old("thumb_num", $video->{'thumb_num'} ?? '') }}" />
            </div>
            <div class="w-full px-3 mt-12" id="pre-existing" style="display:none;">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="pre_existing_video_id">
                    {{ trans("videos.pre-existing videos") }}
                </label>
                <div id="filtered_results" class="mt-2">
                    <input list="pre_existing_videos" name="pre_existing_video_id" class="form_select" id="pre_existing_video_id">
                    <datalist id="pre_existing_videos">
                        @foreach($meridePreExisting as $result)
                        <option value="{{ $result->id }}-{{ $result->title }}" data-id="{{$result->id}}" data-url="{{ $result->video?->url_video_mp4 }}">
                            @endforeach
                    </datalist>
                </div>
            </div>
            <div class="w-full px-3 mt-12">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="tags">
                    {{ trans("general.tags") }}
                </label>
                <input class="form_input" value="{{ old("tags", (isset($video) && !empty($video->tags)) ? implode(', ', $video->tags) : '') }}" type="text" name="tags" placeholder="{{ trans("general.comma separated tags") }}" />
            </div>
            <div class="w-full px-3 mt-12">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="models">
                    {{ trans("general.models") }}
                </label>
                <input class="form_input" value="{{ old("models", (isset($video) && !empty($video->models)) ? implode(', ', $video->models) : '') }}" type="text" name="models" placeholder="{{ trans("general.comma separated models") }}" />
            </div>
            <div class="w-full px-3 mt-12" id="ce_text_container">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="ce_text">
                    {{ trans("videos.ce_text") }}
                </label>
                <input class="form_input" type="text" name="ce_text" id="ce_text_id" placeholder="{{ trans("videos.ce_text") }}" required value="{{ old("ce_text", $video->ce_text ?? '') }}" />
            </div>

            <div class="w-full md:w-1/2 px-3 mt-12">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="related">
                    {{ trans("videos.related") }}
                </label>
                <div class="flex relative w-64">
                    <select name="related[]" class="form_select" multiple size='8' id="related">
                        @foreach ($published_videos as $v)
                        <option {{ in_array($v->id, old("related", $program->related ?? [])) ? 'selected' : '' }} value="{{ $v->id }}">{{ $v->title }}</option>
                        @endforeach
                    </select>
                    <a href="#" onclick="deselectAll(document.getElementById('related'))">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </a>
                    <script type="text/javascript">
                        function deselectAll(select) {
                            select.selectedIndex = -1;
                        }
                    </script>
                </div>
            </div>
            <div class="w-full md:w-1/2 px-3 mt-12">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                    {{ trans("general.image") }}
                </label>
                @if (isset($video) && !empty($video->image))
                <img src="{{ $video->image->url }}" title="{{ $video->image->name }}" />
                @endif
                <input type="file" name="image" accept="image/png, image/jpeg, image/webp" />
                <p><i>{{ trans("general.peso massimo immagine") }} {{ ini_get('upload_max_filesize') }}</i></p>
            </div>
            @if($video?->type != App\Enums\VideoType::EXT_VIEW->value)
            <div class="w-full md:w-1/2 px-3 mt-12" id="preview_video" style="display:block;">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                    {{ trans("general.preview video") }}
                </label>

                @include('videos.render', ['entity' => $video ?? null, 'preview' => true])

                <div id="drag-drop-area-preview"></div>

            </div>

            <div class="w-full md:w-1/2 px-3 mt-12" id="main_video" style="display:block;">
                <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                    {{ trans("general.main video") }}
                </label>

                @include('videos.render', ['entity' => $video ?? null, 'preview' => true])

                <div id="drag-drop-area"></div>

            </div>
            @endif
            <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="scheduled_at">
                {{ trans("general.published at") }}
            </label>
            <input class="form_input" type="datetime-local" name="published_at" placeholder="{{ trans("general.published at") }}" required value="{{ old("published_at", $video?->published_at ?? now()) }}" />
        </div>

            <div class="w-full px-3 mt-12">
                <div class="basis-1/2">
                @if (!isset($video) or $video?->status != App\Enums\VideosStatus::PUBLISHED->value)
                    <button id="save-button" class="btn_save" type="submit">{{ trans("general.Save draft") }}</button>
                    @elseif($video?->status==App\Enums\ModelStatus::PUBLISHED->value)
                    <button id="save-button" class="btn_save" type="submit">{{ trans("general.save") }}</button>
                    @endif
                </div>
                @if (isset($video) and $video->canPublish() and (!$video->isPublished()))
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
        document.addEventListener("DOMContentLoaded", function() {
            const pre_exist_value=sessionStorage.getItem('pre-existingValue');
            const pre_type=sessionStorage.getItem('selectedType');
            const preExisting = document.getElementById('pre-existing');
            var preExistingVideoId = document.getElementById('meride_video_id').value;
            var preExistingSelect = document.getElementById("pre_existing_video_id");
            var options = document.querySelectorAll("#pre_existing_videos option");
            const istypeSelect = document.getElementById('type');
            preExisting.style.display = 'none';
            console.log("------------>",pre_type);

            var selectedOption = Array.from(options).find(function(option) {
                return option.getAttribute("data-id") === preExistingVideoId;
            });

            if (selectedOption) {
                preExistingSelect.value = selectedOption.value;
            } else {
                console.log("No matching option found");
            }
            if(pre_exist_value)
            {
                const id=pre_exist_value.split('-');
                document.getElementById('meride_video_id').value=id[0];
                preExisting.style.display = 'block';
                sessionStorage.removeItem('pre-existingValue')
            }
            else{
                preExisting.style.display = 'none';
            }
        });
        const istypeSelect = document.getElementById('type');
        const video360Field = document.getElementById('ext_view_url_field');
        const thumb_num_container = document.getElementById('thumb_num_container');
        const thumb_num = document.getElementById('thumb_num');
        const preview_video = document.getElementById('preview_video');
        const main_video = document.getElementById('main_video');
        const video360Input = document.getElementById('ext_view_url');
        const preExisting = document.getElementById('pre-existing');
        const pre_value=document.getElementById("pre_existing_video_id");
        console.log("--------------->",istypeSelect.value);
        sessionStorage.setItem('selectedType', istypeSelect.value);
        console.log("------------>",sessionStorage.getItem('selectedType'));
        pre_value.addEventListener('change', function() {
        sessionStorage.setItem('pre-existingValue',pre_value.value);
        });
       
        if (istypeSelect.value === 'EXT_VIEW') {
            video360Field.style.display = 'block';
            thumb_num_container.style.display='block';
            preview_video.style.display = 'none';
            main_video.style.display = 'none';
            video360Input.setAttribute('required', 'required');
            thumb_num.setAttribute('required','required');
        } else if (istypeSelect.value === 'PRE_EXISTING') {
            document.getElementById("meride_video_id").value = video_id;
            preExisting.style.display = 'block';
            preview_video.style.display = 'none';
            main_video.style.display = 'none';
            video360Field.style.display = 'none';
            thumb_num_container.style.display='none';
            video360Input.removeAttribute('required');
            thumb_num.removeAttribute('required');
        } else {
            video360Field.style.display = 'none';
            thumb_num_container.style.display='none';
            video360Input.removeAttribute('required');
            thumb_num.removeAttribute('required');
        }
        istypeSelect.addEventListener('change', function() {
            sessionStorage.setItem('selectedType', istypeSelect.value);
            if (this.value === 'EXT_VIEW') {
                video360Field.style.display = 'block';
                thumb_num_container.style.display='block';
                preview_video.style.display = 'none';
                main_video.style.display = 'none';
                preExisting.style.display = 'none';
                video360Input.setAttribute('required', 'required');
                thumb_num.setAttribute('required','required');
            } else if (istypeSelect.value === 'PRE_EXISTING') {
                video360Field.style.display = 'none';
                preExisting.style.display = 'block';
                preview_video.style.display = 'none';
                thumb_num_container.style.display='none';
                main_video.style.display = 'none';
                video360Input.value = null;
                video360Input.removeAttribute('required');
                thumb_num.removeAttribute('required');
            } else {
                video360Field.style.display = 'none';
                preExisting.style.display = 'none';
                thumb_num_container.style.display='none';
                preview_video.style.display = 'block';
                main_video.style.display = 'block';
                video360Input.value = null;
                video360Input.removeAttribute('required');
                thumb_num.removeAttribute('required');
            }
        });
        const createForm = document.getElementById("video-form");
        const publishButton = document.getElementById("publish-button");
        publishButton.addEventListener("click", ev => {
            ev.preventDefault();
            document.getElementById("status").value = "PUBLISHED";
            createForm.submit();
            return false;
        });
    </script>
</x-layouts.panel_layout>