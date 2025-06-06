<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BuffetController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\DecorationController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\ResetPassword;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SatisfactionSurveyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Route;


Route::get('/login_api', [AuthenticatedSessionController::class, 'login_api'])->name('login_api');

Route::get('/', function () {
    return redirect(config('app.administrative_url'));
})->name('home');
// Route::get('/{buffet}', [SiteController::class,'buffetTest'])->name('buffetTest');
Route::get('/buffet', [SiteController::class,'buffetAlegria'])->name('buffetTest');

// Todas as rotas de landing page caso existam devem ser feitas aqui, antes dos middlewares

Route::get('/{buffet}/booking/calendar', [BookingController::class,'calendar'])->name('booking.calendar');


Route::middleware(['auth', 'verified'])->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', [SiteController::class, 'dashboard'])->name('dashboard');
}); 


Route::get('{buffet}/booking/{booking}/guest/',[GuestController::class, 'create'])->name('guest.invite');
Route::post('{buffet}/booking/{booking}/guest',[GuestController::class, 'store'])->name('guest.store');


Route::middleware(['buffet-exists', 'auth', 'verified'])->group(function () {
    Route::get('/{buffet}/calendar', [BookingController::class,'buffet_calendar'])->name('calendar');

    Route::get('{buffet}/dashboard', [BuffetController::class, 'dashboard'])->name('buffet.dashboard');
    Route::get('{buffet}', [BuffetController::class, 'dashboard']);
    Route::get('{buffet}/edit', [BuffetController::class, 'edit'])->name('buffet.edit');
    Route::put('{buffet}', [BuffetController::class, 'update'])->name('buffet.update');
    Route::put('{buffet}/logo', [BuffetController::class, 'update_logo'])->name('buffet.update_logo');
    
    
    Route::patch('{buffet}/booking/{booking}/guest/{guest}/change_status',[GuestController::class,'change_status'])->name('guest.change_status');
    
    Route::get('{buffet}/booking/{booking}/guest/{guest}',[GuestController::class, 'show'])->name('guest.show');

    Route::patch('/{buffet}/food/{food}/change_status', [FoodController::class,'change_status'])->name('food.change_status');
    Route::patch('/{buffet}/food/{food}/activate', [FoodController::class,'activate_food'])->name('food.activate_food');
    Route::patch('/{buffet}/food/{food}/{foods_photo}',[FoodController::class,'update_photo'])->name('food.update_photo');
    Route::resource('{buffet}/food', FoodController::class);

    Route::resource('{buffet}/decoration', DecorationController::class);
    Route::patch('/{buffet}/decoration/{decoration}/activate', [DecorationController::class,'activate_decoration'])->name('decoration.activate_decoration');
    Route::patch('/{buffet}/decoration/{decoration}/change_status', [DecorationController::class,'change_status'])->name('decoration.change_status');
    Route::patch('/{buffet}/decoration/{decoration}/{decoration_photos} ',[DecorationController::class,'update_photo'])->name('decoration.update_photo');

    Route::resource('{buffet}/schedule', ScheduleController::class);
    Route::patch('/{buffet}/schedule/{schedule}/change_status', [ScheduleController::class,'change_status'])->name('schedule.change_status');

    Route::resource('{buffet}/employee', EmployeeController::class);
    
    Route::get('/{buffet}/booking/party_mode', [BookingController::class, 'party_mode'])->name('booking.party_mode');
    Route::get('/{buffet}/booking/list', [BookingController::class, 'list'])->name('booking.list');
    Route::get('/{buffet}/booking/me', [BookingController::class, 'my_bookings'])->name('booking.my_bookings');
    Route::patch('/{buffet}/booking/{booking}/change_status', [BookingController::class,'change_status'])->name('booking.change_status');
    Route::patch('/{buffet}/booking/{booking}/reschedule', [BookingController::class,'reschedule_party'])->name('booking.reschedule');
    Route::resource('{buffet}/booking', BookingController::class);

    Route::patch('/{buffet}/survey/{survey}/change_question_status', [SatisfactionSurveyController::class,'change_question_status'])->name('survey.change_status');
    Route::resource('{buffet}/survey', SatisfactionSurveyController::class);
    Route::post('/{buffet}/survey/answer', [SatisfactionSurveyController::class, 'answer_question'])->name('survey.answer_question');

    Route::post('/survey/answer', [SatisfactionSurveyController::class, 'answer'])->name('survey.answer');
    Route::resource('{buffet}/recommendation',RecommendationController::class);
    Route::patch('/{buffet}/recommendation/{recommendation}/change_status', [RecommendationController::class,'change_status'])->name('recommendation.change_status');

	Route::get('/{buffet}/profile', [UserProfileController::class, 'show'])->name('profile');
	Route::post('/{buffet}/profile', [UserProfileController::class, 'update'])->name('profile.update');
    
    Route::resource('{buffet}/user',UserController::class);
    Route::patch('/{buffet}/user/{user}/change_role', [UserController::class,'change_role'])->name('user.change_role');

    Route::get('{buffet}/configurations', [ConfigurationController::class, 'index'])->name('configurations.index');
    // Route::get('{buffet}/configurations', [ConfigurationController::class, 'edit'])->name('configurations.edit');
    Route::post('{buffet}/configurations', [ConfigurationController::class, 'update'])->name('configurations.update');
});
            

// Route::get('/reset-password', [ResetPassword::class, 'show'])->middleware('guest')->name('reset-password');
// Route::post('/reset-password', [ResetPassword::class, 'send'])->middleware('guest')->name('reset.perform');
// Route::get('/change-password', [ChangePassword::class, 'show'])->middleware('guest')->name('change-password');
// Route::post('/change-password', [ChangePassword::class, 'update'])->middleware('guest')->name('change.perform');

require __DIR__.'/auth.php';