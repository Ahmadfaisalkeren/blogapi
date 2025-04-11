<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\HeroController;
use App\Http\Controllers\API\PostsController;
use App\Http\Controllers\API\SeriesController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\SeriesPartController;


Route::post('login', [LoginController::class, 'login']);
Route::get('/user/{id}', [LoginController::class, 'user']);

Route::middleware('auth:sanctum')->group(function () {

    Route::match(['post', 'put'], '/post/{id?}', [PostsController::class, 'store']);
    Route::post('/uploadImage', [PostsController::class, 'storeTemporaryImage']);
    Route::delete('/deleteImage/{type}/{id}', [PostsController::class, 'deleteImage']);
    Route::delete('post/{id}', [PostsController::class, 'destroy']);
    Route::post('upload/image', [PostsController::class, 'upload']);

    Route::post('hero', [HeroController::class, 'store']);
    Route::get('hero/{id}', [HeroController::class, 'edit']);
    Route::put('hero/{id}', [HeroController::class, 'update']);
    Route::delete('hero/{id}', [HeroController::class, 'destroy']);

    Route::post('series', [SeriesController::class, 'store']);
    Route::get('series/{id}', [SeriesController::class, 'edit']);
    Route::put('series/{id}', [SeriesController::class, 'update']);
    Route::delete('series/{id}', [SeriesController::class, 'destroy']);

    Route::match(['post', 'put'], '/seriesPart/{id?}', [SeriesPartController::class, 'store']);
    Route::put('seriesPart/{seriesPartId}', [SeriesPartController::class, 'update']);
    Route::delete('seriesPart/{seriesPartId}', [SeriesPartController::class, 'destroy']);
    Route::get('seriesParty/{id}', [SeriesPartController::class, 'edit']);

});
Route::get('dashboard', [DashboardController::class, 'index']);

Route::get('seriesParty/{seriesPartId}/show', [SeriesPartController::class, 'show']);
Route::get('posts', [PostsController::class, 'index']);
Route::get('publishedPosts', [PostsController::class, 'publishedPosts']);
Route::get('post/{id}', [PostsController::class, 'showById']);
Route::get('post/{slug}/show', [PostsController::class, 'show']);
Route::get('heroes', [HeroController::class, 'index']);
Route::get('series', [SeriesController::class, 'index']);
Route::get('series/{slug}/show', [SeriesController::class, 'show']);
Route::get('publishedSeries', [SeriesController::class, 'publishedSeries']);
Route::get('seriesParts', [SeriesPartController::class, 'index']);
Route::get('seriesPart/{seriesSlug}', [SeriesPartController::class, 'index']);

Route::get('phpinfo', function () {
    phpinfo();
});
