<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageUploadController;
use Illuminate\Support\Facades\Storage;

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

Route::get('/debug-s3', function () {
    return [
        'env_endpoint' => env('AWS_ENDPOINT'),
        'config_endpoint' => config('filesystems.disks.s3.endpoint'),
        'bucket' => config('filesystems.disks.s3.bucket'),
        'can_write' => Storage::disk('s3')->put('debug.json', '{"test": "ok"}'),
    ];
});
