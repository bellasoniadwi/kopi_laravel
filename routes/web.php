<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\ResetController;
use App\Http\Controllers\KopiController;
use App\Http\Controllers\RecordController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Auth::routes();
//reset password
Route::post('login/{provider}/callback', 'Auth\LoginController@handleCallback');
Route::get('/email/verify', [ResetController::class, 'verify_email'])->name('verify');
Route::get('/password/reset', [ResetController::class, 'index'])->name('index');
Route::post('/password/reset/store', [ResetController::class, 'store'])->name('store');

Route::group(['middleware' => ['auth', 'notpetani']], function () {
    Route::get('/', [HomeController::class, 'dashboard'])->name('dashboard');

    // index
    Route::get('/record', [RecordController::class, 'index'])->name('record');
    Route::get('/kopi', [KopiController::class, 'index'])->name('kopi');
    Route::get('/user', [UserController::class, 'index'])->name('user.index');

    // create
    Route::get('/create-user', [UserController::class, 'create_form'])->name('user.form');
    Route::post('/create-user', [UserController::class, 'create'])->name('user.create');
    Route::get('/create-kopi', [KopiController::class, 'create_form'])->name('kopi.form');
    Route::post('/create-kopi', [KopiController::class, 'create'])->name('kopi.create');

    // edit
    Route::get('/edit-kopi/{id}', [KopiController::class, 'edit_form'])->name('kopi.form.edit');
    Route::post('/edit-kopi/{id}', [KopiController::class, 'update'])->name('kopi.update');
    Route::get('/edit-record/{id}', [RecordController::class, 'edit_form'])->name('record.form.edit');
    Route::post('/edit-record/{id}', [RecordController::class, 'update'])->name('record.update');
    Route::get('/edit-user/{id}', [UserController::class, 'edit_form'])->name('user.form.edit');
    Route::post('/edit-user/{id}', [UserController::class, 'update'])->name('user.update');

    // export
    Route::get('/export-record', [RecordController::class, 'exportExcel'])->name('export.record');
    Route::get('/export-kopi', [KopiController::class, 'exportExcel'])->name('export.kopi');
    Route::get('/export-users', [UserController::class, 'exportExcel'])->name('export.users');

    // delete
    Route::delete('/delete-kopi/{id}', [KopiController::class, 'delete'])->name('kopi.delete');
    Route::delete('/delete-record/{id}', [RecordController::class, 'delete'])->name('record.delete');
    Route::delete('/delete-user/{id}', [UserController::class, 'delete'])->name('user.delete');

    // import
    Route::post('/import-kopi', [KopiController::class, 'importExcel'])->name('import.kopi');
    Route::post('/import-users', [UserController::class, 'importExcel'])->name('import.users');
});


Route::get('/not-authorize', [HomeController::class, 'notauthorize'])->name('notauthorize');