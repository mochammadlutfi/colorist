<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Settings\GeneralSetting;
use App\Models\Locale;
use App\Http\Controllers\TestController;
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

Route::get('/test', [TestController::class, 'index']);
Route::get('/test/kirim', [TestController::class, 'sendData']);

Route::get('{path}', function () {
    $data['locales'] = Locale::latest()->get();
    return view('app', compact('data'));
})->where('path', '.*');
