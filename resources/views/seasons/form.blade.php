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

    <form  id="season-form" class="p-12" action="{{ $formType == 'create' ? route('seasons.store') : route('seasons.update', $season->id) }}" method="POST" enctype="multipart/form-data">
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
        
        <input type="hidden" id="status" name="status" value="{{$season->status ?? 'DRAFT'}}" />

        <div class="w-full md:w-1/2 px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="program_id">
                {{ trans("programs.Program") }}
            </label>
            <div class="inline-block relative w-64">
                <select id="program_id" name="program_id" class="form_select" onchange="{{ $formType == 'create' ? 'nextSeasonNumber()' : '' }}" required >
                    @foreach ($programs as $program)
                    <option {{ old("program_id", $season->program_id ?? '') == $program->id ? 'selected' : '' }} value="{{ $program->id }}">{{ $program->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="w-full md:w-1/2 px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="order_number">
                {{ trans("seasons.season number") }}
            </label>
            <div class="inline-block relative w-64">
                <select id="order_number" name="order_number" class="form_select" required >
                    @foreach (range(1, 20) as $number)
                    <option {{ old("order_number", $season->order_number ?? '') == $number ? 'selected' : '' }} value="{{ $number }}">{{ $number }}</option>
                    @endforeach
                </select>
                <div id="order_number_loader" style="display: none">
                    <x-loading-spinner></x-loading-spinner>
                </div>
            </div>
        </div>

        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="title">
                {{ trans("general.title") }}
            </label>
            <input class="form_input" type="text" name="title" placeholder="{{ trans("general.title") }}" value="{{ old("title", $season->title ?? '') }}" required maxlength="255" />
        </div>

        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="description">
                {{ trans("general.content") }}
            </label>

            <div id="description" class="editor_textarea"></div>
            <input type="hidden" name="description">
            <script type="text/plain" id="season_source_description">{!! old("description", $season->description ?? '') !!}</script>
        </div>

        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="short_description">
                {{ trans("general.short_description") }}
            </label>
            <textarea rows="3" name="short_description" placeholder="{{ trans("general.short_description") }}" class="form_input">{{ old("short_description", $season->short_description ?? '') }}</textarea>
        </div>

        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="tags">
                {{ trans("general.tags") }}
            </label>
            <input class="form_input" value="{{ old("tags", (isset($season) && !empty($season->tags)) ? implode(', ', $season->tags) : '') }}" type="text" name="tags" placeholder="{{ trans("general.comma separated tags") }}" />
        </div>

        
        <div class="w-full md:w-1/2 px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans("general.image") }}
            </label>
            @if (isset($season) && !empty($season->image))
                <img src="{{ $season->image->url }}" title="{{ $season->image->name }}" />
            @endif
            <input type="file" name="image" accept="image/png, image/jpeg, image/webp" />
            <p><i>{{ trans("general.peso massimo immagine") }} {{ ini_get('upload_max_filesize') }}</i></p>
        </div>
        
        <div class="w-full md:w-1/2 px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans("general.poster image") }}
            </label>
            @if (isset($season) && !empty($season->imagePoster))
                <img src="{{ $season->imagePoster->url }}" title="{{ $season->imagePoster->name }}" />
            @endif
            <input type="file" name="image_poster" accept="image/png, image/jpeg, image/webp" />
            <p><i>{{ trans("general.peso massimo immagine") }} {{ ini_get('upload_max_filesize') }}</i></p>
        </div>

        <div class="w-full md:w-1/2 px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans("general.preview video") }}
            </label>

            @include('videos.render', ['entity' => $season ?? null, 'preview' => true])

            <div id="drag-drop-area-preview"></div>

        </div>

        <div class="w-full md:w-1/2 px-3 mt-12">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans("general.main video") }}
            </label>

            @include('videos.render', ['entity' => $season ?? null, 'preview' => false])

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
@vite(['resources/js/seasons/form.js'])
@endpush

<script>
const nextSeasonNumber = () => {
    document.getElementById("order_number").setAttribute('disabled', true);
    document.getElementById("order_number_loader").style.display = "block";
    const program_id = document.getElementById("program_id").value;
    fetch(`${BASE_URL}/admin/programs/next_season_number?program_id=${program_id}`)
            .then(res => res.json())
            .then(result => {
                document.getElementById("order_number").value = result;
                document.getElementById("order_number").removeAttribute('disabled');
                document.getElementById("order_number_loader").style.display = "none";
            })
            .catch(err => {
                document.getElementById("order_number").removeAttribute('disabled');
                document.getElementById("order_number_loader").style.display = "none";
            });
}
</script>

@if($formType == 'create')
<script>
    nextSeasonNumber();
</script>
@endif

</x-layouts.panel_layout>
