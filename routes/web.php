<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\PemeriksaanController;
use App\Http\Controllers\Api\ObatAjaxController;
use App\Http\Controllers\PembayaranController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = Auth::user();

    return match ($user->role) {
        'dokter' => redirect()->route('dokter.dashboard'),
        'apoteker' => redirect()->route('apoteker.dashboard'),
        default => abort(403, 'Role tidak dikenal.'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/medicines', [MedicineController::class, 'index'])->name('medicines.index');
    Route::get('/ajax/obat', [ObatAjaxController::class, 'autocomplete'])->name('ajax.obat.autocomplete');
    Route::get('/ajax/harga-obat', [ObatAjaxController::class, 'harga']);
});

Route::middleware(['auth', 'role:dokter'])->group(function () {
    Route::get('/dokter/dashboard', function () {
        return view('dokter.dashboard');
    })->name('dokter.dashboard');

    Route::resource('pemeriksaan', PemeriksaanController::class);
});

Route::middleware(['auth', 'role:apoteker'])->group(function () {
    Route::get('/apoteker/dashboard', function () {
        return view('apoteker.dashboard');
    })->name('apoteker.dashboard');

    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
    Route::get('/pembayaran/{pemeriksaan}', [PembayaranController::class, 'show'])->name('pembayaran.show');
    Route::patch('/pembayaran/{pemeriksaan}/selesai', [PembayaranController::class, 'selesaikan'])->name('pembayaran.selesai');
    Route::get('/pembayaran/{pemeriksaan}/cetak', [PembayaranController::class, 'cetak'])->name('pembayaran.cetak');
});

require __DIR__.'/auth.php';
