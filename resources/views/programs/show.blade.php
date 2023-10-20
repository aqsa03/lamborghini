@push('scripts')

@vite(['resources/js/programs/show.js'])

@endpush

<x-layouts.panel_layout>

<div class="body_section">

<a href="{{ route('programs.index') }}" title="trans('programs.Programs list')">&lsaquo; {{ trans('programs.Back to all programs') }}</a>

<div class="pr-4 flex gap-4 items-end place-content-end relative w-full">

    <a href="{{ route("programs.edit", $program->id) }}" class="btn_new_entity text-center inline-flex items-center" >{{ trans('general.edit') }}</a>

    <form action="{{ route('programs.destroy', $program->id) }}" method="POST">
        @csrf
        {{ method_field('DELETE') }}
        <button data-delete-message="Stai per cancellare il programma {{ $program->title }}. Sei sicuro?" class="delete-link delete_btn" type="submit">{{ trans('general.delete') }}</button>
    </form>
</div>

<h1 class="font-bold text-2xl py-8">{{ $program->title }}</h1>

<div class="md:flex">
    <div class="md:basis-2/3 px-8">

        <div class="w-full mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.description') }}
            </label>
            <div id="description" class="editor_textarea"></div>
            <script type="text/plain" id="source_description">{!! $program->description !!}</script>
        </div>

        <div class="w-full mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.short_description') }}
            </label>
            <div>
                {{ $program->short_description }}
            </div>
        </div>

        <div class="w-full mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.tags') }}
            </label>
            <div>
                {{ !empty($program->tags) ? implode(', ', $program->tags) : '' }}
            </div>
        </div>

        <div class="w-full mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.image') }}
            </label>
            @if($program->image)
                <img src="{{ $program->image->url }}" title="{{ $program->image->name }}" />
            @endif
        </div>

        <div class="w-full mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.poster image') }}
            </label>
            @if($program->imagePoster)
                <img src="{{ $program->imagePoster->url }}" title="{{ $program->imagePoster->name }}" />
            @endif
        </div>

        <div class="w-full mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.preview video') }}
            </label>
            @include('videos.render', ['entity' => $program, 'preview' => true])
        </div>

        <div class="w-full mt-12">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.main video') }}
            </label>
            @include('videos.render', ['entity' => $program, 'preview' => false])
        </div>

    </div>

    <aside class="basis-1/3 border-l p-6">
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('programs.related') }}
            </label>
            @if(!empty($program->related))
            <ul>
                @foreach($program->related as $r)
                    <li><a href="{{ route("programs.show", $r) }}">{{ App\Models\Program::find($r)->title }}</a></li>
                @endforeach
            </ul>
            @endif
        </div>
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('programs.podcast') }}
            </label>
            {{ trans('general.'.($program->podcast ? 'YES' : 'NO')) }}
        </div>
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.status') }}
            </label>
            <x-badges.program_status :status="$program->status">{{ $program->status }}</x-badges.program_status>
        </div>
        @if ($program->status === App\Enums\ProgramStatus::PUBLISHED->value)
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.published at') }}
            </label>
            <div>
                {{ Carbon\Carbon::createFromDate($program->published_at)->format("d-m-Y H:i") }}
            </div>
        </div>
        @endif
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.created at') }}
            </label>
            <div>
                {{ Carbon\Carbon::createFromDate($program->created_at)->format("d-m-Y H:i") }}
            </div>
        </div>
        <div class="aside_info mt-8">
            <label class="mp-12 block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                {{ trans('general.updated at') }}
            </label>
            <div>
                {{ Carbon\Carbon::createFromDate($program->updated_at)->format("d-m-Y H:i") }}
            </div>
        </div>
    </aside>

</div>



</x-layouts.panel_layout>
