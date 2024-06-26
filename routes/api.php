<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\ProductController;

Route::controller(RegisterController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('products', ProductController::class);
});

Route::post('update-user/{userId}', 'App\Http\Controllers\API\RegisterController@updateUser')->middleware('auth:sanctum');
Route::post('register', 'App\Http\Controllers\API\RegisterController@register')->middleware('auth:sanctum');
Route::get('me', 'App\Http\Controllers\API\RegisterController@me')->middleware('auth:sanctum');
Route::post('logout', 'App\Http\Controllers\API\RegisterController@logout')->middleware('auth:sanctum');
Route::delete('user/delete/{id}', 'App\Http\Controllers\API\RegisterController@deleteUser')->middleware('auth:sanctum');
Route::post('upload-image', 'App\Http\Controllers\API\FileController@storeImage');
Route::post('upload-file', 'App\Http\Controllers\API\FileController@storeFile');
Route::get('users', 'App\Http\Controllers\API\RegisterController@getUsers')->middleware('auth:sanctum');

Route::group([
    'prefix' => 'study-programs'
], function () {
    Route::get('/', 'App\Http\Controllers\API\StudyProgramController@getStudyPrograms')->middleware('auth:sanctum');
    Route::get('/with-locations', 'App\Http\Controllers\API\StudyProgramController@getStudyProgramsWithLocations')->middleware('auth:sanctum');
    Route::post('/create', 'App\Http\Controllers\API\StudyProgramController@createStudyProgram')->middleware('auth:sanctum');
    Route::post('update/{studyProgramId}', 'App\Http\Controllers\API\StudyProgramController@updateStudyProgram')->middleware('auth:sanctum');
    Route::delete('delete/{studyProgramId}', 'App\Http\Controllers\API\StudyProgramController@deleteStudyProgram')->middleware('auth:sanctum');
});

Route::group([
    'prefix' => 'locations'
], function () {
    Route::get('/', 'App\Http\Controllers\API\LocationController@getLocations')->middleware('auth:sanctum');
    Route::post('/create', 'App\Http\Controllers\API\LocationController@createLocation')->middleware('auth:sanctum');
    Route::post('update/{locationId}', 'App\Http\Controllers\API\LocationController@updateLocation')->middleware('auth:sanctum');
    Route::delete('delete/{locationId}', 'App\Http\Controllers\API\LocationController@deleteLocation')->middleware('auth:sanctum');
});

Route::group([
    'prefix' => 'categories'
], function () {
    Route::get('/', 'App\Http\Controllers\API\CategoryController@getCategories')->middleware('auth:sanctum');
    Route::post('/create', 'App\Http\Controllers\API\CategoryController@createCategory')->middleware('auth:sanctum');
    Route::post('update/{categoryId}', 'App\Http\Controllers\API\CategoryController@updateCategory')->middleware('auth:sanctum');
    Route::delete('delete/{categoryId}', 'App\Http\Controllers\API\CategoryController@deleteCategory')->middleware('auth:sanctum');
});


Route::group([
    'prefix' => 'quarter-years'
], function () {
    Route::get('/', 'App\Http\Controllers\API\QuarterYearController@getQuarterYears')->middleware('auth:sanctum');
    Route::post('/create', 'App\Http\Controllers\API\QuarterYearController@createQuarterYear')->middleware('auth:sanctum');
    Route::post('update/{quarterYearId}', 'App\Http\Controllers\API\QuarterYearController@updateQuarterYear')->middleware('auth:sanctum');
    Route::delete('delete/{quarterYearId}', 'App\Http\Controllers\API\QuarterYearController@deleteQuarterYear')->middleware('auth:sanctum');
});

Route::group([
    'prefix' => 'assets'
], function () {
    Route::get('/', 'App\Http\Controllers\API\AssetController@getAssets')->middleware('auth:sanctum');
    Route::get('/{assetId}', 'App\Http\Controllers\API\AssetController@getAssetById')->middleware('auth:sanctum');
    Route::post('/create', 'App\Http\Controllers\API\AssetController@createAsset')->middleware('auth:sanctum');
    Route::post('update/{assetId}', 'App\Http\Controllers\API\AssetController@updateAsset')->middleware('auth:sanctum');
    Route::delete('delete/{assetId}', 'App\Http\Controllers\API\AssetController@deleteAsset')->middleware('auth:sanctum');
});

Route::group([
    'prefix' => 'asset-improvements'
], function () {
    Route::get('/', 'App\Http\Controllers\API\AssetImprovementController@getAssetImprovements')->middleware('auth:sanctum');
    Route::post('/create', 'App\Http\Controllers\API\AssetImprovementController@createAssetImprovement')->middleware('auth:sanctum');
    Route::get('asset/{assetId}', 'App\Http\Controllers\API\AssetImprovementController@getAssetImprovementsByAssetId')->middleware('auth:sanctum');
    Route::post('update/{assetId}', 'App\Http\Controllers\API\AssetImprovementController@updateAssetImprovement')->middleware('auth:sanctum');
    Route::post('update-status/{assetId}', 'App\Http\Controllers\API\AssetImprovementController@updateAssetImprovementStatus')->middleware('auth:sanctum');
    Route::delete('delete/{assetId}', 'App\Http\Controllers\API\AssetImprovementController@deleteAssetImprovement')->middleware('auth:sanctum');
});

Route::group([
    'prefix' => 'dashboard'
], function () {
    Route::get('/total-asset-by-study-program', 'App\Http\Controllers\API\DashboardController@getTotalAssetByStudyProgram')->middleware('auth:sanctum');
    Route::get('/asset-log-success', 'App\Http\Controllers\API\DashboardController@getAllAssetLogSuccess')->middleware('auth:sanctum');
    Route::get('/total-price-improvement', 'App\Http\Controllers\API\DashboardController@getTotalImprovementPriceByQuartalYear')->middleware('auth:sanctum');
    Route::get('/percentage-status', 'App\Http\Controllers\API\DashboardController@getPercentageStatusByQuartalYear')->middleware('auth:sanctum');
});