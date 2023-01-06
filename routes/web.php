<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttachmentController;

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

Route::get('/', function () {
    return view('welcome');
});

Route::post('/store/pdf',[AttachmentController::class,'uploadPdf'])->name('store.pdf');
Route::get('/convert/pdf/to/images',[AttachmentController::class,'convertpdftoimages'])->name('convert_pdf_to_images');
Route::get('/add/bar/code/to/images',[AttachmentController::class,'add_bar_code_to_images2'])->name('add_bar_code_to_images');
