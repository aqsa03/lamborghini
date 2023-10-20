<a class="nav_item" title="{{ trans('general.News') }}">{{ trans('general.News') }}</a>
<ul class="ml-4">
    <li>
        <a class="nav_item{{ in_array(Route::currentRouteName(), ['newsCategories.index', 'newsCategories.show', 'newsCategories.edit', 'newsCategories.create']) ? ' nav_item__selected': '' }}" href="{{ route('newsCategories.index') }}" title="{{ trans("categories.View all categories") }}">{{ trans("categories.Categories") }}</a>
        <a class="nav_item{{ in_array(Route::currentRouteName(), ['news.index', 'news.show', 'news.edit', 'news.create']) ? ' nav_item__selected': '' }}" href="{{ route('news.index') }}" title="{{ trans("news.View all news") }}">{{ trans("news.News") }}</a>
    </li>
</li>