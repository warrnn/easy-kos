<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PemilikController;
use App\Http\Controllers\PenghuniController;
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

Route::get('/', function () {
    return redirect()->route('authentication.login');
});

Route::get('/authentication/login', [AuthenticationController::class, 'login_form'])->name('authentication.login');
Route::post('/authentication/login', [AuthenticationController::class, 'authenticate']);
Route::get('/authentication/register', [AuthenticationController::class, 'register_form'])->name('authentication.register');
Route::post('/authentication/register', [AuthenticationController::class, 'add_user']);
Route::get('/authentication/logout', [AuthenticationController::class, 'logout'])->name('authentication.logout');
Route::post('/authentication/logout', [AuthenticationController::class, 'logout'])->name('authentication.logout');


// Route::get('/admin/index', [AdminController::class, 'index']);
// Route::get('/pemilik_kos/index', [PemilikController::class, 'index']);
// Route::get('/penghuni/index', [PenghuniController::class, 'index']);

Route::middleware(['role:admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/manage-users', [AdminController::class, 'manageUsers'])->name('admin.manage-users');
    Route::delete('/admin/manage-users/delete/{pengguna:id}', [AdminController::class, 'deleteUser'])->name('admin.manage-users.delete');
    Route::delete('/admin/manage-users/delete/kos/{kos:id}', [AdminController::class, 'deleteKos'])->name('admin.manage-users.kos.delete');

    Route::get('/admin/form-pemilik', [AdminController::class, 'formKos'])->name('admin.form-pemilik');
    Route::post('/admin/form-pemilik/add-kos', [AdminController::class, 'addKos'])->name('admin.form-pemilik.add-kos');
    Route::get('/admin/form-pemilik/edit/{kos:id}', [AdminController::class, 'formEditKos'])->name('admin.form-pemilik.edit');
    Route::post('/admin/form-pemilik/edit/{kos:id}', [AdminController::class, 'editKos'])->name('admin.form-pemilik.edit');
    
    Route::get('/admin/form-users', [AdminController::class, 'formAddUsers'])->name('admin.form-users');
    Route::post('/admin/form-users/add-user', [AdminController::class, 'addUsers'])->name('admin.form-users.add-user');
    Route::get('/admin/form-users/edit/{pengguna:id}', [AdminController::class, 'formEditUsers'])->name('admin.form-users.edit');
    Route::post('/admin/form-users/edit/{pengguna:id}', [AdminController::class, 'editUser'])->name('admin.form-users.edit');
});

Route::middleware(['role:pemilik'])->group(function () {
    Route::get('/pemilik_kos/index', [PemilikController::class, 'showKos'])->name('pemilik.index');
    Route::post('/pemilik_kos/buat-kos', [PemilikController::class, 'addKos'])->name('kos.add-kos');
    Route::get('/pemilik_kos/request', [PemilikController::class, 'indexRequest'])->name('pemilik.request.index');
    Route::get('/pemilik_kos/laporan', [PemilikController::class, 'indexLaporan'])->name('pemilik.laporan.index');
    Route::get('/pemilik_kos/laporan/addKamar', [PemilikController::class, 'addKamar'])->name('pemilik.laporan.kamar.index');
    Route::post('/pemilik_kos/laporan/addKamar/{id_kos}', [PemilikController::class, 'storeKamar'])->name('pemilik.laporan.kamar.store');
    Route::put('/pemilik-kos/request/update-status/{pesanan}', [PemilikController::class, 'updateStatus'])->name('pemilik_kos.request.updateStatus');
});

Route::middleware(['role:penghuni'])->group(function () {
    Route::get('/penghuni/index', [PenghuniController::class, 'showAllKos'])->name('penghuni.index');
    Route::get('/penghuni/kos/index/{kos}', [PenghuniController::class, 'showAllKamar'])->name('penghuni.kos.index');
    Route::get('/penghuni/pemesanan/index', [PenghuniController::class, 'showPemesanan'])->name('penghuni.pemesanan.index');
    Route::get('/kos/{id}', [PenghuniController::class, 'show'])->name('kos.show');
    Route::post('/penghuni/kos/pesan/{kamarId:id}', [PenghuniController::class, 'pesanKamar'])->name('penghuni.kos.pesan');
    Route::get('/penghuni/review/{kosId:id}', [PenghuniController::class, 'formReview'])->name('penghuni.review');
    Route::post('/penghuni/review/{kosId:id}', [PenghuniController::class, 'addReview'])->name('penghuni.review');
});
