@push('scripts')
@endpush
<x-layouts.panel_layout>

    <div class="body_section">

        <a href="{{ route('videos.index') }}" title="trans('videos.Videos list')">&lsaquo; {{ trans('videos.Back to all videos') }}</a>

        <div class="pr-4 flex gap-4 items-end place-content-end relative w-full">

            <a href="{{ route("videos.edit", $video->id) }}" class="btn_new_entity text-center inline-flex items-center">{{ trans('general.edit') }}</a>

            <form action="{{ route('videos.destroy', $video->id) }}" method="POST">
                @csrf
                {{ method_field('DELETE') }}
                <button data-delete-message="Stai per cancellare l'episodio {{ $video->title }}. Sei sicuro?" class="delete-link delete_btn" type="submit">{{ trans('general.delete') }}</button>
            </form>
        </div>

        <h1 class="font-bold text-2xl py-8">{{ $video->title }}</h1>

        <div class="md:flex">
            <div class="md:basis-2/3 px-8">

                <div class="w-full mt-12">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                        {{ trans('general.description') }}
                    </label>
                    <div id="description" class="editor_textarea" data-description="{{ $video->description }}">
                        {{ $video->description }}
                    </div>
                    <script type="text/plain" id="source_description">{{ $video->description }}</script>
                </div>
                @if(!empty($video->ce_text))
                @foreach(['ca', 'ch', 'kr', 'tw', 'uk', 'us', 'eu'] as $key)
                <div class="w-full px-3 mt-12" id="ce_text_{{ $key }}_container">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="ce_text_{{ $key }}">
                        {{ trans("videos.ce_text_$key") }}
                    </label>
                    @php
                        $ceTextArray = json_decode($video?->ce_text, true);
                        $ceTextValue = $ceTextArray[$key] ?? ''; // Get value from array or default to empty string
                    @endphp
                    <input class="form_input" type="text" name="ce_text_{{ $key }}" id="ce_text_{{ $key }}_id" placeholder="{{ trans("videos.ce_text_$key") }}" value="{{ old("ce_text_$key", $ceTextValue) }}" readonly />
                </div>
                @endforeach

                <!-- <div class="w-full mt-12" id="ce_text">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="ce_text">
                        {{ trans("videos.ce_text") }}
                    </label>
                    <div style="border: 1px solid #ccc;padding: 8px;cursor: pointer;border-radius: 10px; width: 830px;">
                        {{ $video->ce_text }}
                    </div>
                </div> -->
                @endif
                <div class="w-full mt-12">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                        {{ trans('general.tags') }}
                    </label>
                    <div>

                        {{ !empty($video->tags) ? implode(', ', $video->tags) : '' }}
                    </div>
                </div>
                @if(!empty($video->related))
                <div class="w-full mt-12">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="related">
                        {{ trans("videos.related") }}
                    </label>
                    <div class="flex relative w-64">
                        <select name="related[]" class="form_select" multiple size='8' id="related">
                            @foreach ($published_videos->whereIn('id', $video->related) as $v)
                            <option selected value="{{ $v->id }}">{{ $v->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif
                @if(!empty($video->models))
                <div class="w-full mt-12">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="models">
                        {{ trans("general.models") }}
                    </label>
                    <div class="flex relative w-64">
                        <select name="models[]" class="form_select" multiple size='8' id="models">
                            @foreach ($published_ce_models->whereIn('id', $video->models) as $v)
                            <option selected value="{{ $v->id }}">{{ htmlspecialchars($v->title) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif

                @if($video->ext_view_url)
                <div class="w-full mt-12" id="ext_view_url_field">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="ext_view_url">
                        {{ trans("videos.ext_view_url") }}
                    </label>
                    <div style="border: 1px solid #ccc;padding: 8px;cursor: pointer;border-radius: 10px; width: 830px;" onclick="window.location.href='{{ old("{'ext_view_url'}", $video->ext_view_url ?? '') }}';">
                        {{ $video->ext_view_url }}
                    </div>
                </div>
                <div class="w-full mt-12" id="thumb_num">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="thumb_num">
                        {{ trans("videos.thumb_num") }}
                    </label>
                    <div style="border: 1px solid #ccc;padding: 8px;cursor: pointer;border-radius: 10px; width: 830px;" onclick="window.location.href='{{ old("{'thumb_num'}", $video->thumb_num ?? '') }}';">
                        {{ $video->thumb_num }}
                    </div>
                </div>
                @endif
                <div class="w-full mt-12">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                        {{ trans("general.image") }}
                    </label>
                    @if (isset($video) && !empty($video->image))
                    <img src="{{ $video->image->url }}" title="{{ $video->image->name }}" />
                    @endif
                </div>

                @if(!$video->ext_view_url)
                <div class="w-full mt-12">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                        {{ trans('general.preview video') }}
                    </label>
                    @include('videos.render', ['entity' => $video, 'preview' => true])
                </div>
                <div class="w-full mt-12">
                    <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                        {{ trans('general.main video') }}
                    </label>
                    @include('videos.render', ['entity' => $video, 'preview' => true])
                </div>
                @endif
            </div>

            <aside class="basis-1/3 border-l p-6">
                <div class="aside_info mt-8">
                    <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                        {{ trans('videos.Category') }}
                    </label>
                    <span>{{ $video->category->title ?? '' }}</span>
                </div>
                <div class="aside_info mt-8">
                    <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                        {{ trans('videos.Model') }}
                    </label>
                    <span>{{ $video->model->title ?? '' }}</span>
                </div>
                <div class="aside_info mt-8">
                    <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                        {{ trans('videos.product video') }}
                    </label>
                    <div>
                        {{ $video->product_video == 1 ? 'True' : 'False' }}
                    </div>
                </div>
                <div class="aside_info mt-8">
                    <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                        {{ trans('videos.subtitles') }}
                    </label>
                    <div>
                        {{ $video->subtitles == 1 ? 'True' : 'False' }}
                    </div>
                </div>

                <div class="aside_info mt-8">
                    <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                        {{ trans('general.status') }}
                    </label>
                    {{-- <x-badges.episode_status :status="$video->status"> --}}
                        {{ $video->status }}
                    {{-- </x-badges.episode_status> --}}
                </div>
                @if ($video->status === App\Enums\VideosStatus::PUBLISHED->value)
                <div class="aside_info mt-8">
                    <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                        {{ trans('general.published at') }}
                    </label>
                    <div>
                        {{ Carbon\Carbon::createFromDate($video->published_at)->format("d-m-Y H:i") }}
                    </div>
                </div>
                @endif
                <div class="aside_info mt-8">
                    <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                        {{ trans('general.created at') }}
                    </label>
                    <div>
                        {{ Carbon\Carbon::createFromDate($video->created_at)->format("d-m-Y H:i") }}
                    </div>
                </div>
                <div class="aside_info mt-8">
                    <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                        {{ trans('general.updated at') }}
                    </label>
                    <div>
                        {{ Carbon\Carbon::createFromDate($video->updated_at)->format("d-m-Y H:i") }}
                    </div>
                </div>
            </aside>

        </div>
</x-layouts.panel_layout>