<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PengajuanKapalIkan;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// auth
Route::post('/register', [AuthController::class, 'register']);
Route::get('/register-confirmation/{email}/{otp}', [AuthController::class, 'confirmation_link'])->name('confirmation_link');
Route::post('/register-confirmation', [AuthController::class, 'confirmation_api'])->name('confirmation_api');
Route::post('/login', [AuthController::class, 'login'])->name('login');
// auth
Route::middleware('auth:api')->group(function () {
    Route::get('/verifikasi-user', [AuthController::class, 'list_verifikasi_user'])->name('list-verifikasi-user')->middleware('role:admin');
    Route::post('/terima-user/{id_user}', [AuthController::class, 'terima_user'])->name('terima-user')->middleware('role:admin');
    Route::post('/tolak-user/{id_user}', [AuthController::class, 'tolak_user'])->name('tolak-user')->middleware('role:admin');
});

// users
Route::middleware('auth:api')->group(function () {
    Route::get('/users', [UserController::class, 'list_users'])->name('list-user')->middleware('role:admin');
    Route::put('/users', [UserController::class, 'edit_profile'])->name('edit-profile')->middleware('role:user');
    Route::get('/users/{id}', [UserController::class, 'detail_users'])->name('detail-user')->middleware('role:admin');
    Route::delete('/users/{id}', [UserController::class, 'delete_users'])->name('delete-user')->middleware('role:admin');
});

// kapal ikan pengajuan
Route::middleware('auth:api')->group(function() {
    Route::post('/pengajuan-kapal-ikan', [PengajuanKapalIkan::class, 'pengajuan'])->name('pengajuan-kapal-ikan')->middleware('role:user');
    Route::delete('/kapal-ikan/{id}', [PengajuanKapalIkan::class, 'hapus'])->name('hapus-kapal-ikan');
    Route::put('/edit-pengajuan-kapal-ikan/{id}', [PengajuanKapalIkan::class, 'edit'])->name('edit-pengajuan-kapal-ikan'); // admin & user
    Route::get('/list-pengajuan-kapal-ikan', [PengajuanKapalIkan::class, 'list'])->name('list-pengajuan-kapal-ikan')->middleware('role:admin');
    Route::post('/terima-pengajuan-kapal-ikan/{id}', [PengajuanKapalIkan::class, 'terima'])->name('terima-pengajuan-kapal-ikan')->middleware('role:admin');
    Route::post('/tolak-pengajuan-kapal-ikan/{id}', [PengajuanKapalIkan::class, 'tolak'])->name('tolak-pengajuan-kapal-ikan')->middleware('role:admin');
    Route::post('/generate-token', [PengajuanKapalIkan::class, 'generate_token'])->name('generate-token')->middleware('role:admin');
    Route::get('/list-token', [PengajuanKapalIkan::class, 'list_token'])->name('list-token')->middleware('role:admin');
    Route::delete('/delete-token/{token}', [PengajuanKapalIkan::class, 'delete_token'])->name('delete-token')->middleware('role:admin');
});
Route::get('/kapal-ikan/{id}', [PengajuanKapalIkan::class, 'detail'])->name('detail-kapal-ikan');