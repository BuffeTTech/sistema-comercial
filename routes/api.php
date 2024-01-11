<?php

use App\Http\Controllers\SubscriptionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/subscription', [SubscriptionController::class, 'create_subscription'])->name('buffet.subscription.store');
Route::post('/subscription/permission', [SubscriptionController::class, 'create_permission'])->name('buffet.permission.store');
Route::post('/subscription/role', [SubscriptionController::class, 'create_role'])->name('buffet.role.store');
// Route::post('/subscription/permission/{permission}', [SubscriptionController::class, 'create_permission'])->name('buffet.permission.store');
Route::post('/subscription/permission/{permission}', [SubscriptionController::class, 'insert_role_in_permission'])->name('buffet.permission.insert_role');
Route::delete('/subscription/permission/{permission}', [SubscriptionController::class, 'remove_role_from_permission'])->name('buffet.permission.remove_role');