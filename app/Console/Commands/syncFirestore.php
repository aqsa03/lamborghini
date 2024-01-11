<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Category;
use App\Models\Post;
use App\Jobs\PushUserToFirebase;
use App\Jobs\PushCategoryToFirebase;
use App\Jobs\PushPostToFirebase;
use App\Enums\PostStatus;
use App\Jobs\PushEpisodeToFirebase;
use App\Jobs\PushLiveToFirebase;
use App\Jobs\PushPalimpsestItemToFirebase;
use App\Jobs\PushPageSectionToFirebase;
use App\Jobs\PushPageToFirebase;
use App\Jobs\PushPalimpsestTemplateItemToFirebase;
use App\Models\Live;

use App\Models\Page;
use App\Models\PageSection;
use App\Models\PalimpsestItem;
use App\Models\PalimpsestTemplateItem;


use App\Models\Image;
use App\Models\Video;
use App\Models\ModelVideo;
use App\Jobs\PushModelVideoToFirebase;
use App\Models\CarModel;
use App\Jobs\PushCarModelToFirebase;

class syncFirestore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'firestore:sync {collections=models,categories,lives,pages,page_sections,videos}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push current DB data to firestore. Only Push, no cancellations are performed';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $collections = explode(',', $this->argument('collections'));
        foreach($collections as $collection) {
            switch($collection) {
                case "authors" :
                    User::all()
                        ->each(function ($user) {
                            PushUserToFirebase::dispatch($user);
                        });
                    $this->info("Sync collection  ".$collection);
                    break;
    
                case "categories":
                    Category::all()
                        ->each(function ($category) {
                            PushCategoryToFirebase::dispatch($category);
                        });
                    $this->info("Sync collection  ".$collection);
                    break;

                case "lives":
                    Live::all()
                        ->each(function ($live) {
                            PushLiveToFirebase::dispatch($live);
                        });
                    $this->info("Sync collection  ".$collection);
                    break;

                case "pages":
                    Page::all()
                        ->each(function ($page) {
                            PushPageToFirebase::dispatch($page);
                        });
                    $this->info("Sync collection  ".$collection);
                    break;

                case "page_sections":
                    PageSection::all()
                        ->each(function ($section) {
                            PushPageSectionToFirebase::dispatch($section);
                        });
                    $this->info("Sync collection  ".$collection);
                    break;

                case "videos":
                    ModelVideo::all()
                        ->each(function ($video) {
                            PushModelVideoToFirebase::dispatch($video);
                        });
                    $this->info("Sync collection  ".$collection);
                    break;

                case "models":
                    CarModel::all()
                        ->each(function ($model) {
                            PushCarModelToFirebase::dispatch($model);
                        });
                    $this->info("Sync collection  ".$collection);
                    break;

                default:
                    $this->error("No Model found for collection  ".$collection);
                    break;
            }
        }
    }
}
