<a class="nav_item{{ in_array(Route::currentRouteName(), ['categories.index', 'categories.show', 'categories.edit']) ? ' nav_item__selected': '' }}" href="{{ route('categories.index') }}" title="{{ trans("categories.View all categories") }}">{{ trans("categories.Categories") }}</a>
<ul class="ml-4">
    <li>
        <a class="nav_item{{ Route::currentRouteName() == 'categories.create' ? ' nav_item__selected': '' }}" href="{{ route('categories.create') }}" title="{{ trans("categories.New category") }}">{{ trans("categories.New category") }}</a>
    </li>
</li>
