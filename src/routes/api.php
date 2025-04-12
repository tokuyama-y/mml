<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/upload', function (\Illuminate\Http\Request $request) {
    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('uploads', 'public');
        return response()->json(['path' => asset('storage/' . $path)]);
    }
    return response()->json(['error' => 'No image uploaded'], 400);
});
