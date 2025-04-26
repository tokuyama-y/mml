<?php

use App\Http\Controllers\HaikuFromImageController;
use App\Http\Controllers\ImageAbstractController;
use App\Http\Controllers\MindscapeResultController;
use App\Http\Controllers\Simple\AbstractImageGeneratorController;
use App\Http\Controllers\Simple\HaikuGeneratorController;
use Illuminate\Support\Facades\Route;

//use App\Http\Controllers\ImageUploadController;

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

Route::post('/abstract-image', [ImageAbstractController::class, 'generate']);
Route::post('/image-haiku', [HaikuFromImageController::class, 'generate']);
Route::post('/upload-image', [HaikuFromImageController::class, 'generate']);
Route::get('/mindscape-results', [MindscapeResultController::class, 'index']);

// simple apis
Route::post('/haiku-generator', [HaikuGeneratorController::class, 'generate']);
Route::post('/karesansui-generator', [AbstractImageGeneratorController::class, 'generate']);
