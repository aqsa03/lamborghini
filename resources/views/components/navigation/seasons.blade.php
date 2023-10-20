<a class="nav_item{{ in_array(Route::currentRouteName(), ['seasons.index', 'seasons.show', 'seasons.edit']) ? ' nav_item__selected': '' }}" href="{{ route('seasons.index') }}" title="{{ trans('seasons.View all seasons') }}">{{ trans('seasons.Seasons') }}</a>
<ul class="ml-4">
    <li>
        <a class="nav_item{{ Route::currentRouteName() == 'seasons.create' ? ' nav_item__selected': '' }}" href="{{ route('seasons.create') }}" title="{{ trans('seasons.New season') }}">{{ trans('seasons.New season') }}</a>
    </li>
</li>
