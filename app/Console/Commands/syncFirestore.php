<?php

namespace App\Console\Commands;

use App\Enums\EpisodeStatus;
use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Category;
use App\Models\Post;
use App\Jobs\PushUserToFirebase;
use App\Jobs\PushCategoryToFirebase;
use App\Jobs\PushPostToFirebase;
use App\Enums\PostStatus;
use App\Enums\ProgramStatus;
use App\Enums\SeasonStatus;
use App\Enums\NewsStatus;
use App\Jobs\PushEpisodeToFirebase;
use App\Jobs\PushLiveToFirebase;
use App\Jobs\PushNewsCategoryToFirebase;
use App\Jobs\PushPalimpsestItemToFirebase;
use App\Jobs\PushProgramToFirebase;
use App\Jobs\PushSeasonToFirebase;
use App\Jobs\PushNewsToFirebase;
use App\Jobs\PushPageSectionToFirebase;
use App\Jobs\PushPageToFirebase;
use App\Jobs\PushPalimpsestTemplateItemToFirebase;
use App\Models\Episode;
use App\Models\Live;
use App\Models\NewsCategory;
use App\Models\News;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\PalimpsestItem;
use App\Models\PalimpsestTemplateItem;
use App\Models\Program;
use App\Models\Season;

class syncFirestore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'firestore:sync {collections=authors,categories,episodes,programs,seasons,lives,palimpsest_items,palimpsest_template,news_categories,news,pages,page_sections}';

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

                case "episodes":
                    Episode::where('status', '=', EpisodeStatus::PUBLISHED->value)
                        ->each(function ($episode) {
                            PushEpisodeToFirebase::dispatch($episode);
                        });
                    $this->info("Sync collection  ".$collection);
                    break;
                
                case "seasons":
                    Season::where('status', '=', SeasonStatus::PUBLISHED->value)
                        ->each(function ($season) {
                            PushSeasonToFirebase::dispatch($season);
                        });
                    $this->info("Sync collection  ".$collection);
                    break;

                case "programs":
                    Program::where('status', '=', ProgramStatus::PUBLISHED->value)
                        ->each(function ($program) {
                            PushProgramToFirebase::dispatch($program);
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

                case "palimpsest_items":
                    PalimpsestItem::all()
                        ->each(function ($item) {
                            PushPalimpsestItemToFirebase::dispatch($item);
                        });
                    $this->info("Sync collection  ".$collection);
                    break;

                case "palimpsest_template":
                    PalimpsestTemplateItem::all()
                        ->each(function ($item) {
                            PushPalimpsestTemplateItemToFirebase::dispatch($item);
                        });
                    $this->info("Sync collection  ".$collection);
                    break;

                case "news_categories":
                    NewsCategory::all()
                        ->each(function ($category) {
                            PushNewsCategoryToFirebase::dispatch($category);
                        });
                    $this->info("Sync collection  ".$collection);
                    break;

                case "news":
                    News::where('status', '=', NewsStatus::PUBLISHED->value)
                        ->each(function ($news) {
                            PushNewsToFirebase::dispatch($news);
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

                default:
                    $this->error("No Model found for collection  ".$collection);
                    break;
            }
        }
    }
}
