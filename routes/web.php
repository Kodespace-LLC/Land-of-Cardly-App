<?php

use App\Http\Controllers\ViewHandlerController;
use Illuminate\Support\Facades\Route;

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
Route::post('preview',[ViewHandlerController::class,"preview"])->middleware(['allowcors'])->name('previewcard');
Route::get('test',[ViewHandlerController::class,"testing"])->name('testing');
Route::get('/', [ViewHandlerController::class,"homeview"])->middleware(['verify.shopify'])->name('home');