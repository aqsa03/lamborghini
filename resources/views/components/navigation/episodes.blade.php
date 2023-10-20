<a class="nav_item{{ in_array(Route::currentRouteName(), ['episodes.index', 'episodes.show', 'episodes.edit']) ? ' nav_item__selected': '' }}" href="{{ route('episodes.index') }}" title="{{ trans('episodes.View all episodes') }}">{{ trans('episodes.Episodes') }}</a>
<ul class="ml-4">
    <li>
        <a class="nav_item{{ Route::currentRouteName() == 'episodes.create' ? ' nav_item__selected': '' }}" href="{{ route('episodes.create') }}" title="{{ trans('episodes.New episode') }}">{{ trans('episodes.New episode') }}</a>
    </li>
</li>
