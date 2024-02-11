<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\BuffetController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\DecorationController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SatisfactionSurveyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Todas as rotas de landing page caso existam devem ser feitas aqui, antes dos middlewares

Route::get('/{buffet}/booking/calendar', [BookingController::class,'calendar'])->name('booking.calendar');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', [SiteController::class, 'dashboard'])->name('dashboard');
});


Route::middleware(['buffet-exists', 'auth', 'verified'])->group(function () {
    Route::get('{buffet}/dashboard', [BuffetController::class, 'dashboard'])->name('buffet.dashboard');
    Route::get('{buffet}', [BuffetController::class, 'dashboard']);
    
    Route::get('{buffet}/booking/{booking}/guest/invite/',[GuestController::class, 'create'])->name('guest.invite');
    Route::patch('{buffet}/booking/{booking}/guest/{guest}/change_status',[GuestController::class,'change_status'])->name('guest.change_status');
    Route::post('{buffet}/booking/{booking}/guest',[GuestController::class, 'store'])->name('guest.store');
    Route::get('{buffet}/booking/{booking}/guest/{guest}',[GuestController::class, 'show'])->name('guest.show');

    Route::patch('/{buffet}/food/{food}/change_status', [FoodController::class,'change_status'])->name('food.change_status');
    Route::patch('/{buffet}/food/{food}/activate', [FoodController::class,'activate_food'])->name('food.activate_food');
    Route::patch('/{buffet}/food/{food}/{foods_photo} ',[FoodController::class,'update_photo'])->name('food.update_photo');
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
    Route::patch('/{buffet}/booking/{booking}/change_status', [BookingController::class,'change_status'])->name('booking.change_status');
    Route::patch('/{buffet}/booking/{booking}/reschedule', [BookingController::class,'reschedule_party'])->name('booking.reschedule');
    Route::resource('{buffet}/booking', BookingController::class);

    Route::patch('/{buffet}/survey/{survey}/change_question_status', [SatisfactionSurveyController::class,'change_question_status'])->name('survey.change_status');
    Route::resource('{buffet}/survey', SatisfactionSurveyController::class);
    Route::post('/{buffet}/survey/answer', [SatisfactionSurveyController::class, 'answer_question'])->name('survey.answer_question');

    //Route::post('/survey/answer', [SatisfactionSurveyController::class, 'answer'])->name('survey.answer');
    Route::resource('{buffet}/recommendation',RecommendationController::class);
    Route::patch('/{buffet}/recommendation/{recommendation}/change_status', [RecommendationController::class,'change_status'])->name('recommendation.change_status');


});


require __DIR__.'/auth.php';
