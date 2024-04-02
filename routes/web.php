<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\CameraController;
use App\Http\Controllers\HarianController;
use App\Http\Controllers\KontrolAlatController;
use App\Http\Controllers\PenetasanController;
use App\Http\Controllers\ProfilController;
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
Route::middleware(['guest'])->group(function () {
    Route::get('/', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/logout', [AuthController::class, 'logout']);

    Route::get('/beranda', [BerandaController::class, 'index'])->name('beranda');
    Route::get('/beranda/grafik_penetasan', [PenetasanController::class, 'grafik'])->name('beranda.penetasan.grafik');
    Route::get('/beranda/grafik_suhu', [KontrolAlatController::class, 'grafik'])->name('beranda.suhu.grafik');

    Route::get('/penetasan', [PenetasanController::class, 'index'])->name('penetasan');
    Route::get('/penetasan/grafik', [PenetasanController::class, 'grafik'])->name('penetasan.grafik');
    Route::post('/penetasan/create', [PenetasanController::class, 'create']);
    Route::put('/penetasan/{id_penetasan}/edit', [PenetasanController::class, 'edit']);
    Route::delete('/penetasan/{id_penetasan}/delete', [PenetasanController::class, 'delete']);
    Route::get('/penetasan/{id_penetasan}/harian', [HarianController::class, 'index']);
    Route::get('/penetasan/{id_penetasan}/harian/create', [HarianController::class, 'index_create']);
    Route::post('/penetasan/{id_penetasan}/harian/create', [HarianController::class, 'create']);
    Route::get('/penetasan/{id_penetasan}/harian/{id_harian}/edit', [HarianController::class, 'index_edit']);
    Route::put('/penetasan/{id_penetasan}/harian/{id_harian}/edit', [HarianController::class, 'edit']);
    Route::delete('/penetasan/{id_penetasan}/harian/{id_harian}/delete', [HarianController::class, 'delete']);

    Route::get('/kontrolalat', [KontrolAlatController::class, 'index'])->name('kontrolalat');
    Route::get('/kontrolalat/grafik', [KontrolAlatController::class, 'grafik'])->name('kontrolalat.grafik');
    
    Route::get('/profil/{id_peternak}', [ProfilController::class, 'index'])->name('profil');
    Route::put('/profil/{id_peternak}/edit', [ProfilController::class, 'edit']);
    Route::post('/profil/password', [ProfilController::class, 'changePassword']);
});

