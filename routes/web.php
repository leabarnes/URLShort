<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShortLinkController;

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

Route::get('generate-shorten-link', [ShortLinkController::class,'index']);

Route::get('get-link-data', [ShortLinkController::class,'getLinkData']);

Route::post('generate-shorten-link', [ShortLinkController::class,'store'])->name('generate.shorten.link.post');
   
Route::get('{code}', [ShortLinkController::class,'shortenLink'])->name('shorten.link');
