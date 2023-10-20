<a class="nav_item{{ Route::currentRouteName() == 'users.index' ? ' nav_item__selected': '' }}" href="{{ route('users.index') }}" title="{{ trans('navigation.View all users') }}">{{ trans_choice('navigation.user', 2) }}</a>
<ul class="ml-4">
    <li>
        <a class="nav_item{{ Route::currentRouteName() == 'users.create' ? ' nav_item__selected': '' }}" href="{{ route('users.create') }}" title="{{ trans('navigation.New user') }}">{{ trans('navigation.New user') }}</a>
    </li>
</li>
