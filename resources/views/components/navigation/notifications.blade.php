<a class="nav_item{{ Route::currentRouteName() == 'notifications.index' ? ' nav_item__selected': '' }}" href="{{ route('notifications.index') }}" title="{{ trans('navigation.View all notifications') }}">{{ trans_choice('navigation.notification', 2) }}</a>
<ul class="ml-4">
    <li>
        <a class="nav_item{{ Route::currentRouteName() == 'notifications.create' ? ' nav_item__selected': '' }}" href="{{ route('notifications.create') }}" title="{{ trans('navigation.Create a notification') }}">{{ trans('navigation.New notification') }}</a>
    </li>
</li>
