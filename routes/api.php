<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\BuffetController;
use App\Http\Controllers\DecorationController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\SatisfactionSurveyController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::get('/{buffet}/booking/calendar', [BookingController::class,'api_calendar'])->name('api.booking.calendar');


// Route::get('/{buffet}/booking/schedule/{day}', [BookingController::class, 'api_get_open_schedules_by_day_and_buffet'])->name('api.bookings.get_schedules_by_day_buffet');
Route::get('/{buffet}/booking/schedule/{day}/edit', [BookingController::class, 'api_get_open_schedules_by_day_and_buffet_update'])->name('api.bookings.get_schedules_by_day_buffet_update');

Route::get('/{buffet}/booking/schedule/{day}', [BookingController::class, 'api_get_schedules_by_day'])->name('api.bookings.get_schedules_by_day');
Route::get('/{buffet}/booking/schedule/{day}/{time}/disponibility', [BookingController::class, 'api_get_disponibility_by_day'])->name('api.bookings.get_disponibility_by_day');

Route::get('/{buffet}/booking/schedule/{birthday}/birthday', [BookingController::class, 'api_get_schedules_by_birthday_date'])->name('api.bookings.get_schedules_by_birthday_date');

Route::get('/{buffet}/food/{food}', [FoodController::class,'api_get_food'])->name('api.food.show');
Route::get('/{buffet}/decoration/{decoration}', [DecorationController::class,'api_get_decoration'])->name('api.decoration.show');


Route::post('/buffet', [BuffetController::class, 'store_buffet_api'])->name('api.buffet.store');
Route::put('/buffet/{slug}', [BuffetController::class, 'update_buffet_api'])->name('api.buffet.update');
Route::delete('/buffet/{slug}', [BuffetController::class, 'delete_buffet_api'])->name('api.buffet.delete');

Route::post('/subscription', [SubscriptionController::class, 'create_subscription'])->name('buffet.subscription.store');
Route::post('/subscription/permission', [SubscriptionController::class, 'create_permission'])->name('buffet.permission.store');
Route::post('/subscription/permission/many', [SubscriptionController::class, 'create_many_permission'])->name('buffet.permission.store_many');
Route::post('/subscription/role', [SubscriptionController::class, 'create_role'])->name('buffet.role.store');
// Route::post('/subscription/permission/{permission}', [SubscriptionController::class, 'create_permission'])->name('buffet.permission.store');
Route::post('/subscription/permission/{permission}', [SubscriptionController::class, 'insert_role_in_permission'])->name('buffet.permission.insert_role');
Route::delete('/subscription/permission/{permission}', [SubscriptionController::class, 'remove_role_from_permission'])->name('buffet.permission.remove_role');


Route::get('/{buffet}/survey/user/{user_id}', [SatisfactionSurveyController::class, 'api_get_question_by_user_id'])->name('api.bookings.get_questions_by_user_id'); //API

Route::post('/presentation', [SiteController::class, 'presentation_data']);