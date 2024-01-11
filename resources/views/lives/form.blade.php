<x-layouts.panel_layout>

    <div class="body_section">
    
        <x-form_errors :errors="$errors"></x-form_errors>
    
        <form  id="live-form" class="p-12" action="{{ $formType == 'create' ? route('lives.store') : route('lives.update', $live->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            {{ $formType == 'edit' ? method_field('PUT') : '' }}
    
            <div class="w-full px-3 mt-12">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="title">
                    {{ trans("general.title") }}
                </label>
                <input class="form_input" type="text" name="title" placeholder="{{ trans("general.title") }}" value="{{ old("title", $live->title ?? '') }}" required maxlength="255" />
            </div>
    
            <!-- <div class="w-full px-3 mt-12">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="podcast">
                    {{ trans("lives.podcast") }}
                </label>
                <select name="podcast" class="form_select" required>
                    <option {{ strtolower(old("podcast", $live->podcast ?? '')) == '0' ? 'selected' : '' }} value="0">No</option>
                    <option {{ strtolower(old("podcast", $live->podcast ?? '')) == '1' ? 'selected' : '' }} value="1">Si</option>
                </select>
            </div> -->
    
            <div class="w-full px-3 mt-12">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="meride_embed_id">
                    {{ trans("lives.meride embed id") }}
                </label>
                <div class="inline-block relative w-64" id="meride_embed_id">
                    <select name="meride_embed_id" class="form_select" required >
                        @foreach ($merideEmbedLives as $embedLive)
                        <option {{ old("meride_embed_id", $live->meride_embed_id ?? '') == $embedLive->id ? 'selected' : '' }} value="{{ $embedLive->id }}">{{ $embedLive->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
    
            <!-- <div class="w-full px-3 mt-12">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="description">
                    {{ trans("general.content") }}
                </label>
    
                <div id="description" class="editor_textarea"></div>
                <input type="hidden" name="description">
                <script type="text/plain" id="live_source_description">{!! old("description", $live->description ?? '') !!}</script>
            </div> -->
    
            <div class="w-full px-3 mt-12">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="short_description">
                    {{ trans("general.short_description") }}
                </label>
                <textarea rows="3" name="short_description" placeholder="{{ trans("general.short_description") }}" class="form_input">{{ old("short_description", $live->short_description ?? '') }}</textarea>
            </div>
    
            <div class="w-full px-3 mt-12">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="tags">
                    {{ trans("general.tags") }}
                </label>
                <input class="form_input" value="{{ old("tags", (isset($live) && !empty($live->tags)) ? implode(', ', $live->tags) : '') }}" type="text" name="tags" placeholder="{{ trans("general.comma separated tags") }}" />
            </div>
    
            
            <div class="w-full md:w-1/2 px-3 mt-12">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                    {{ trans("general.image") }}
                </label>
                @if (isset($live) && !empty($live->image))
                    <img src="{{ $live->image->url }}" title="{{ $live->image->name }}" />
                @endif
                <input type="file" name="image" accept="image/png, image/jpeg, image/webp" />
                <p><i>{{ trans("general.peso massimo immagine") }} {{ ini_get('upload_max_filesize') }}</i></p>
            </div>
    
            <div class="w-full md:w-1/2 px-3 mt-12">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                    {{ trans("general.poster image") }}
                </label>
                @if (isset($live) && !empty($live->imagePoster))
                    <img src="{{ $live->imagePoster->url }}" title="{{ $live->imagePoster->name }}" />
                @endif
                <input type="file" name="image_poster" accept="image/png, image/jpeg, image/webp" />
                <p><i>{{ trans("general.peso massimo immagine") }} {{ ini_get('upload_max_filesize') }}</i></p>
            </div>
    
            <div class="w-full px-3 mt-12">
                <div class="basis-1/2">
                    <button id="save-button" class="btn_save" type="submit">{{ trans("general.save") }}</button>
                </div>
            </div>
    
        </form>
    </div>
    
    @push("scripts")
    <!-- @vite(['resources/js/lives/form.js']) -->
    @endpush<a class="nav_item" title="{{ trans('general.Live') }}">{{ trans('lives.Live') }}</a>
    <ul class="ml-4">
        <li>
            <a class="nav_item{{ in_array(Route::currentRouteName(), ['lives.index', 'lives.show', 'lives.edit', 'lives.create']) ? ' nav_item__selected': '' }}" href="{{ route('lives.index') }}" title="{{ trans("lives.View all lives") }}">{{ trans("lives.Live") }}</a>
        </li>
        <!-- <li>
            <a class="nav_item{{ in_array(Route::currentRouteName(), ['palimpsestItems.index', 'palimpsestItems.show', 'palimpsestItems.edit', 'palimpsestItems.create']) ? ' nav_item__selected': '' }}" href="{{ route('palimpsestItems.index') }}" title="{{ trans("palimpsestItems.TV Palimpsest") }}">{{ trans("palimpsestItems.TV Palimpsest") }}</a>
        </li>
        <li>
            <a class="nav_item{{ in_array(Route::currentRouteName(), ['palimpsestTemplateItems.index', 'palimpsestTemplateItems.show', 'palimpsestTemplateItems.edit', 'palimpsestTemplateItems.create']) ? ' nav_item__selected': '' }}" href="{{ route('palimpsestTemplateItems.index') }}" title="{{ trans("palimpsestTemplateItems.Radio Palimpsest") }}">{{ trans("palimpsestTemplateItems.Radio Palimpsest") }}</a>
        </li> -->
    </li>
    </x-layouts.panel_layout>