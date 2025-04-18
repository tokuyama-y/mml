<?php

use App\Http\Controllers\HaikuFromImageController;
use App\Http\Controllers\ImageAbstractController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageUploadController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::post('/upload-image', [ImageUploadController::class, 'upload']);

Route::post('/abstract-image', [ImageAbstractController::class, 'generate']);

Route::post('/image-haiku', [HaikuFromImageController::class, 'generate']);
