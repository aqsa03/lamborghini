<a class="nav_item" title="{{ trans('general.VOD') }}">{{ trans('general.VOD') }}</a>
<ul class="ml-4">
    <li>
        <a class="nav_item{{ in_array(Route::currentRouteName(), ['categories.index', 'categories.show', 'categories.edit', 'categories.create']) ? ' nav_item__selected': '' }}" href="{{ route('categories.index') }}" title="{{ trans("categories.View all categories") }}">{{ trans("categories.Categories") }}</a>
        <a class="nav_item{{ in_array(Route::currentRouteName(), ['model.index', 'model.show', 'model.edit', 'model.create']) ? ' nav_item__selected': '' }}" href="{{ route('model.index') }}" title="{{ trans('model.View all models') }}">{{ trans('episodes.Model') }}</a>
        <a class="nav_item{{ in_array(Route::currentRouteName(), ['programs.index', 'programs.show', 'programs.edit', 'programs.create']) ? ' nav_item__selected': '' }}" href="{{ route('programs.index') }}" title="{{ trans('programs.View all programs') }}">{{ trans('programs.Programs') }}</a>
        <a class="nav_item{{ in_array(Route::currentRouteName(), ['seasons.index', 'seasons.show', 'seasons.edit', 'seasons.create']) ? ' nav_item__selected': '' }}" href="{{ route('seasons.index') }}" title="{{ trans('seasons.View all seasons') }}">{{ trans('seasons.Seasons') }}</a>
        <a class="nav_item{{ in_array(Route::currentRouteName(), ['episodes.index', 'episodes.show', 'episodes.edit', 'episodes.create']) ? ' nav_item__selected': '' }}" href="{{ route('episodes.index') }}" title="{{ trans('episodes.View all episodes') }}">{{ trans('episodes.Episodes') }}</a>
    </li>
</li>