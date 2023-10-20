<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Models\Category;
use App\Models\CarModel;
use App\Models\Episode;
use App\Observers\CategoryFirebaseObserver;
use App\Models\Video;
use App\Observers\VideoObserver;
use App\Models\Program;
use App\Observers\ProgramFirebaseObserver;
use App\Models\Season;
use App\Observers\SeasonFirebaseObserver;
use App\Models\User;
use App\Observers\UserFirebaseObserver;
use App\Models\Live;
use App\Models\News;
use App\Models\NewsCategory;
use App\Observers\LiveFirebaseObserver;
use App\Models\PalimpsestItem;
use App\Observers\PalimpsestItemFirebaseObserver;
use App\Models\PalimpsestTemplateItem;
use App\Observers\PalimpsestTemplateItemFirebaseObserver;
use App\Models\Notification;
use App\Models\Page;
use App\Models\PageSection;
use App\Observers\NotificationFirebaseObserver;
use App\Models\Setting;
use App\Observers\CarModelFirebaseObserver;
use App\Observers\CategoryTitleObserver;
use App\Observers\EpisodeFirebaseObserver;
use App\Observers\EpisodeNextObserver;
use App\Observers\EpisodeObserver;
use App\Observers\NewsCategoryFirebaseObserver;
use App\Observers\NewsCategoryTitleObserver;
use App\Observers\NewsFirebaseObserver;
use App\Observers\PageFirebaseObserver;
use App\Observers\PageSectionFirebaseObserver;
use App\Observers\ProgramDeleteObserver;
use App\Observers\ProgramPodcastObserver;
use App\Observers\SearchStringObserver;
use App\Observers\SeasonDeleteObserver;
use App\Observers\SeasonOrderNumberObserver;
use App\Observers\SettingFirebaseObserver;

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
        Program::observe(ProgramDeleteObserver::class);
        Season::observe(SeasonDeleteObserver::class);
        if(config('my_firebase.projectId')){
            Category::observe(CategoryFirebaseObserver::class);
            CarModel::observe(CarModelFirebaseObserver::class);
            Program::observe(ProgramFirebaseObserver::class);
            Season::observe(SeasonFirebaseObserver::class);
            Episode::observe(EpisodeFirebaseObserver::class);
            
            Live::observe(LiveFirebaseObserver::class);
            PalimpsestItem::observe(PalimpsestItemFirebaseObserver::class);
            PalimpsestTemplateItem::observe(PalimpsestTemplateItemFirebaseObserver::class);

            NewsCategory::observe(NewsCategoryFirebaseObserver::class);
            News::observe(NewsFirebaseObserver::class);

            Page::observe(PageFirebaseObserver::class);
            PageSection::observe(PageSectionFirebaseObserver::class);

            User::observe(UserFirebaseObserver::class);
            
            Notification::observe(NotificationFirebaseObserver::class);
        }
        Episode::observe(EpisodeObserver::class);
        Episode::observe(EpisodeNextObserver::class);
        Video::observe(VideoObserver::class);
        Program::observe(ProgramPodcastObserver::class);
        Season::observe(SeasonOrderNumberObserver::class);
        Category::observe(CategoryTitleObserver::class);
        NewsCategory::observe(NewsCategoryTitleObserver::class);

        Program::observe(SearchStringObserver::class);
        Season::observe(SearchStringObserver::class);
        Episode::observe(SearchStringObserver::class);
        Live::observe(SearchStringObserver::class);
        News::observe(SearchStringObserver::class);
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
