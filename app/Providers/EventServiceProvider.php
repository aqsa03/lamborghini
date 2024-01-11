<?php

namespace App\Providers;

use App\Models\CarModel;
use App\Observers\CarModelFirebaseObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Models\Category;
use App\Observers\CategoryFirebaseObserver;
use App\Models\Video;
use App\Observers\VideoObserver;
use App\Models\User;
use App\Observers\UserFirebaseObserver;
use App\Models\Live;

use App\Observers\LiveFirebaseObserver;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\Setting;
use App\Observers\CategoryTitleObserver;


use App\Observers\PageFirebaseObserver;
use App\Observers\PageSectionFirebaseObserver;
use App\Observers\SearchStringObserver;

use App\Observers\SettingFirebaseObserver;
use App\Models\ModelVideo;
use App\Observers\ModelVideoFirebaseObserver;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {

        if (config('my_firebase.projectId')) {
            Category::observe(CategoryFirebaseObserver::class);
            CarModel::observe(CarModelFirebaseObserver::class);
            ModelVideo::observe(ModelVideoFirebaseObserver::class);
            ModelVideo::observe(ModelVideoFirebaseObserver::class);
            Live::observe(LiveFirebaseObserver::class);
            Page::observe(PageFirebaseObserver::class);
            PageSection::observe(PageSectionFirebaseObserver::class);
            User::observe(UserFirebaseObserver::class);
        }

        Video::observe(VideoObserver::class);
        Live::observe(SearchStringObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
