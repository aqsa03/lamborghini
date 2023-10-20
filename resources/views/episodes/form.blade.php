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

    <form  id="episode-form" class="p-12" action="{{ $formType == 'create' ? route('episodes.store') : route('episodes.update', $episode->id) }}" method="POST" enctype="multipart/form-data">
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
        
        <input type="hidden" id="status" name="status" value="{{$episode->status ?? 'DRAFT'}}" />

        <div class="w-full md:w-1/2 px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="season_id">
                {{ trans("seasons.Season") }}
            </label>
            <div class="inline-block relative w-64">
                <select id="season_id" name="season_id" class="form_select" required onchange="{{ $formType == 'create' ? 'nextEpisodeNumber()' : '' }}" >
                    @foreach ($seasons as $season)
                    <option {{ old("season_id", $episode->season_id ?? '') == $season->id ? 'selected' : '' }} value="{{ $season->id }}">{{ $season->fullTitle }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="w-full md:w-1/2 px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="order_number">
                {{ trans("episodes.episode number") }}
            </label>
            <div class="inline-block relative w-64">
                <select id="order_number" name="order_number" class="form_select" required >
                    @foreach (range(1, 20) as $number)
                    <option {{ old("order_number", $episode->order_number ?? '') == $number ? 'selected' : '' }} value="{{ $number }}">{{ $number }}</option>
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
            <input class="form_input" type="text" name="title" placeholder="{{ trans("general.title") }}" value="{{ old("title", $episode->title ?? '') }}" required maxlength="255" />
        </div>

        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="description">
                {{ trans("general.content") }}
            </label>

            <div id="description" class="editor_textarea"></div>
            <input type="hidden" name="description">
            <script type="text/plain" id="episode_source_description">{!! old("description", $episode->description ?? '') !!}</script>
        </div>

        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="short_description">
                {{ trans("general.short_description") }}
            </label>
            <textarea rows="3" name="short_description" placeholder="{{ trans("general.short_description") }}" class="form_input">{{ old("short_description", $episode->short_description ?? '') }}</textarea>
        </div>

        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="tags">
                {{ trans("general.tags") }}
            </label>
            <input class="form_input" value="{{ old("tags", (isset($episode) && !empty($episode->tags)) ? implode(', ', $episode->tags) : '') }}" type="text" name="tags" placeholder="{{ trans("general.comma separated tags") }}" />
        </div>

        
        <div class="w-full md:w-1/2 px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans("general.image") }}
            </label>
            @if (isset($episode) && !empty($episode->image))
                <img src="{{ $episode->image->url }}" title="{{ $episode->image->name }}" />
            @endif
            <input type="file" name="image" accept="image/png, image/jpeg, image/webp" />
            <p><i>{{ trans("general.peso massimo immagine") }} {{ ini_get('upload_max_filesize') }}</i></p>
        </div>

        <div class="w-full md:w-1/2 px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans("general.poster image") }}
            </label>
            @if (isset($episode) && !empty($episode->imagePoster))
                <img src="{{ $episode->imagePoster->url }}" title="{{ $episode->imagePoster->name }}" />
            @endif
            <input type="file" name="image_poster" accept="image/png, image/jpeg, image/webp" />
            <p><i>{{ trans("general.peso massimo immagine") }} {{ ini_get('upload_max_filesize') }}</i></p>
        </div>

        <div class="w-full md:w-1/2 px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans("general.preview video") }}
            </label>

            @include('videos.render', ['entity' => $episode ?? null, 'preview' => true])

            <div id="drag-drop-area-preview"></div>

        </div>

        <div class="w-full md:w-1/2 px-3 mt-12">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans("general.main video") }}
            </label>

            @include('videos.render', ['entity' => $episode ?? null, 'preview' => false])

            <div id="drag-drop-area"></div>

        </div>

        <div class="w-full px-3 mt-12">
            <div class="basis-1/2">
                @if (!isset($episode) or $episode?->status != App\Enums\EpisodeStatus::PUBLISHED->value)
                <button id="save-button" class="btn_save" type="submit">{{ trans("general.Save draft") }}</button>
                @endif
            </div>
            @if (isset($episode) and $episode->canPublish())
            <div class="basis-1/2 text-right">
                <button id="publish-button" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" type="submit">{{ trans("general.Publish") }}</button>
            </div>
            @endif
        </div>

    </form>
</div>

@push("scripts")
@vite(['resources/js/episodes/form.js'])
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

<script>
const nextEpisodeNumber = () => {
    document.getElementById("order_number").setAttribute('disabled', true);
    document.getElementById("order_number_loader").style.display = "block";
    const season_id = document.getElementById("season_id").value;
    fetch(`${BASE_URL}/admin/seasons/next_episode_number?season_id=${season_id}`)
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
    nextEpisodeNumber();
</script>
@endif


</x-layouts.panel_layout>
