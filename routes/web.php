<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MedicineController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/medicines', [MedicineController::class, 'index'])->name('medicines.index');
});

Route::middleware(['auth', 'role:dokter'])->group(function () {
    Route::get('/dokter/dashboard', function () {
        return view('dokter.dashboard');
    })->name('dokter.dashboard');
});

Route::middleware(['auth', 'role:apoteker'])->group(function () {
    Route::get('/apoteker/dashboard', function () {
        return view('apoteker.dashboard');
    })->name('apoteker.dashboard');
});

require __DIR__.'/auth.php';
