<?php

use App\Http\Controllers\DecorationController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\ProfileController;
use GuzzleHttp\Psr7\Request;
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
    return view('welcome');
})->name('home');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('{buffet}', function () {
    return view('buffet_dashboard');
})->middleware(['auth', 'verified'])->name('dashboard_buffet');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::resource('{buffet}/package', PackageController::class);
    
    Route::resource('{buffet}/decoration', DecorationController::class);
    Route::patch('/{buffet}/decoration/{decoration}/change_status', [DecorationController::class,'change_status'])->name('decoration.change_status');
    Route::patch('/{buffet}/decoration{decoration/{decoration_photos} ',[DecorationController::class,'update_photo'])->name('decoration.update_photo');
});

require __DIR__.'/auth.php';
