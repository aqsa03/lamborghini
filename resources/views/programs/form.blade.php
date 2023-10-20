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

    <form  id="program-form" class="p-12" action="{{ $formType == 'create' ? route('programs.store') : route('programs.update', $program->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        {{ $formType == 'edit' ? method_field('PUT') : '' }}

        <input type="hidden" id="pay_per_view" name="pay_per_view" value="{{$program->pay_per_view ?? 0}}" />

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
        
        <input type="hidden" id="status" name="status" value="{{$program->status ?? 'DRAFT'}}" />

        <div class="w-full md:w-1/2 px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="category_id">
                {{ trans("general.category") }}
            </label>
            <div class="inline-block relative w-64">
                <select name="category_id" class="form_select" required >
                    @foreach ($categories as $category)
                    <option {{ old("category_id", $program->category_id ?? '') == $category->id ? 'selected' : '' }} value="{{ $category->id }}">{{ $category->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="title">
                {{ trans("general.title") }}
            </label>
            <input class="form_input" type="text" name="title" placeholder="{{ trans("general.title") }}" value="{{ old("title", $program->title ?? '') }}" required maxlength="255" />
        </div>

        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="podcast">
                {{ trans("programs.podcast") }}
            </label>
            <select name="podcast" class="form_select" required>
                <option {{ strtolower(old("podcast", $program->podcast ?? '')) == '0' ? 'selected' : '' }} value="0">No</option>
                <option {{ strtolower(old("podcast", $program->podcast ?? '')) == '1' ? 'selected' : '' }} value="1">Si</option>
            </select>
        </div>

        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="description">
                {{ trans("general.content") }}
            </label>

            <div id="description" class="editor_textarea"></div>
            <input type="hidden" name="description">
            <script type="text/plain" id="program_source_description">{!! old("description", $program->description ?? '') !!}</script>
        </div>

        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="short_description">
                {{ trans("general.short_description") }}
            </label>
            <textarea rows="3" name="short_description" placeholder="{{ trans("general.short_description") }}" class="form_input">{{ old("short_description", $program->short_description ?? '') }}</textarea>
        </div>

        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="tags">
                {{ trans("general.tags") }}
            </label>
            <input class="form_input" value="{{ old("tags", (isset($program) && !empty($program->tags)) ? implode(', ', $program->tags) : '') }}" type="text" name="tags" placeholder="{{ trans("general.comma separated tags") }}" />
        </div>

        <div class="w-full md:w-1/2 px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="related">
                {{ trans("programs.related") }}
            </label>
            <div class="flex relative w-64">
                <select name="related[]" class="form_select" multiple size='8' id="related" >
                    @foreach ($published_programs as $p)
                    <option {{ in_array($p->id, old("related", $program->related ?? [])) ? 'selected' : '' }} value="{{ $p->id }}">{{ $p->title }}</option>
                    @endforeach
                </select>
                <!-- <a href="#" onclick="deselectAll(document.getElementById('related'))">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </a>
                <script type="text/javascript">
                function deselectAll(select) {
                    select.selectedIndex = -1;
                }
                </script> -->
            </div>
        </div>
        
        <div class="w-full md:w-1/2 px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans("general.image") }}
            </label>
            @if (isset($program) && !empty($program->image))
                <img src="{{ $program->image->url }}" title="{{ $program->image->name }}" />
            @endif
            <input type="file" name="image" accept="image/png, image/jpeg, image/webp" />
            <p><i>{{ trans("general.peso massimo immagine") }} {{ ini_get('upload_max_filesize') }}</i></p>
        </div>

        <div class="w-full md:w-1/2 px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans("general.poster image") }}
            </label>
            @if (isset($program) && !empty($program->imagePoster))
                <img src="{{ $program->imagePoster->url }}" title="{{ $program->imagePoster->name }}" />
            @endif
            <input type="file" name="image_poster" accept="image/png, image/jpeg, image/webp" />
            <p><i>{{ trans("general.peso massimo immagine") }} {{ ini_get('upload_max_filesize') }}</i></p>
        </div>

        <div class="w-full md:w-1/2 px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans("general.preview video") }}
            </label>

            @include('videos.render', ['entity' => $program ?? null, 'preview' => true])

            <div id="drag-drop-area-preview"></div>

        </div>

        <div class="w-full md:w-1/2 px-3 mt-12">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans("general.main video") }}
            </label>

            @include('videos.render', ['entity' => $program ?? null, 'preview' => false])

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
@vite(['resources/js/programs/form.js'])
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
