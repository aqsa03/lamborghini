<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\ProgramsController;
use \App\Http\Controllers\SeasonsController;
use \App\Http\Controllers\LiveController;
use \App\Http\Controllers\PageController;
use \App\Http\Controllers\UserController;
use \App\Http\Controllers\AdminController;
use App\Http\Controllers\MembersController;
use \App\Http\Controllers\CategoryController;
use \App\Http\Controllers\NotificationController;
use \App\Http\Controllers\SettingController;
use \App\Http\Controllers\associationMembershipController;
use App\Http\Controllers\EpisodesController;
use App\Http\Controllers\CarModelController;
use App\Http\Controllers\NewsCategoryController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PalimpsestItemsController;
use App\Http\Controllers\PalimpsestTemplateItemsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin');

    Route::get('/page/editor', [PageController::class, 'index']);

    Route::resource('pages', PageController::class)->only([
        'index', 'edit', 'update'
    ]);

    Route::get('/programs/search_by_title', [ProgramsController::class, 'searchByTitle'])->name('programs.search_by_title');
    Route::get('/seasons/search_by_title', [SeasonsController::class, 'searchByTitle'])->name('seasons.search_by_title');
    Route::get('/episodes/search_by_title', [EpisodesController::class, 'searchByTitle'])->name('episodes.search_by_title');
    Route::get('/lives/search_by_title', [LiveController::class, 'searchByTitle'])->name('lives.search_by_title');
    Route::get('/news/search_by_title', [NewsController::class, 'searchByTitle'])->name('news.search_by_title');

    Route::get('/programs/search_by_string', [ProgramsController::class, 'searchByString'])->name('programs.search_by_string');
    Route::get('/seasons/search_by_string', [SeasonsController::class, 'searchByString'])->name('seasons.search_by_string');
    Route::get('/episodes/search_by_string', [EpisodesController::class, 'searchByString'])->name('episodes.search_by_string');
    Route::get('/lives/search_by_string', [LiveController::class, 'searchByString'])->name('lives.search_by_string');
    Route::get('/news/search_by_string', [NewsController::class, 'searchByString'])->name('news.search_by_string');

    Route::get('/programs/next_season_number', [ProgramsController::class, 'nextSeasonNumber'])->name('programs.next_season_number');
    Route::get('/seasons/next_episode_number', [SeasonsController::class, 'nextEpisodeNumber'])->name('seasons.next_episode_number');

    Route::resources([
        'categories' => CategoryController::class,
        'model' => CarModelController::class,
        'programs' => ProgramsController::class,
        'seasons' => SeasonsController::class,
        'episodes' => EpisodesController::class,
        'palimpsestItems' => PalimpsestItemsController::class,
        'palimpsestTemplateItems' => PalimpsestTemplateItemsController::class,
        'newsCategories' => NewsCategoryController::class,
        'news' => NewsController::class
    ]);

    Route::group(['middleware' => 'role:editor'], function() {
        Route::resources([
            'notifications' => NotificationController::class,
        ]);
    });

    Route::group(['middleware' => 'role:admin'], function() {
        Route::resources([
            'users' => UserController::class,
        ]);
        Route::resource('members', MembersController::class)->only([
            'index', 'show'
        ]);
    });

    Route::group(['middleware' => 'role:root'], function() { 
        Route::resource('lives', LiveController::class);
        Route::resource('members', MembersController::class)->only([
            'edit', 'update', 'destroy'
        ]);
    });

    Route::resource('lives', LiveController::class)->only([
        'index', 'show'
    ]);

});

