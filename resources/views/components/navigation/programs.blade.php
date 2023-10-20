<a class="nav_item{{ in_array(Route::currentRouteName(), ['programs.index', 'programs.show', 'programs.edit']) ? ' nav_item__selected': '' }}" href="{{ route('programs.index') }}" title="{{ trans('programs.View all programs') }}">{{ trans('programs.Programs') }}</a>
<ul class="ml-4">
    <li>
        <a class="nav_item{{ Route::currentRouteName() == 'programs.create' ? ' nav_item__selected': '' }}" href="{{ route('programs.create') }}" title="{{ trans('programs.New program') }}">{{ trans('programs.New program') }}</a>
    </li>
</li>
