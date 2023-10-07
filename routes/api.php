<?php

use App\Events\TestPusher;
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

Route::get("test-pusher", function () {
    event(new TestPusher('hello world'));
});

Route::group(["prefix" => "file"], function () {
    Route::get("/", [\App\Http\Controllers\FileController::class, "index"]);
    Route::post("store", [\App\Http\Controllers\FileController::class, "store"]);
});
