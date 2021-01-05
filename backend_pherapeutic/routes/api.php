<?php

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

Route::namespace('Api')->group(function () {
    // Authentication Routes
    Route::namespace('Auth')->group(function () {
        Route::post('/login', 'LoginController@login')->name('user.login');
        Route::post('logout', 'User\HomeController@logout')->name('logout');
        Route::post('/register', 'RegisterController@register')->name('user.register');
        Route::post('/verifyOtp', 'RegisterController@verifyOtp')->name('user.verifyOtp');
        Route::post('/resendOtp', 'RegisterController@resendOtp')->name('user.resendOtp');
        Route::post('/forgotPassword', 'LoginController@forgotPassword')->name('user.forgotPassword');
        Route::post('/resetPassword', 'LoginController@resetPassword')->name('user.resetPassword');
    });

    Route::get('/getLanguages', 'LanguagesController@getLanguages')->name('getLanguages');
    Route::get('/getFaqs', 'FaqsController@getFaqs')->name('getFaqs');

    Route::post('findTherapist', 'User\HomeController@findTherapist')->name('findTherapist');

    // Route::get('/getRating', 'User\RatingController@getRating')->name('getRating');
    // Route::get('/getFeedback', 'User\RatingController@getFeedback')->name('getFeedback');

    Route::middleware(['auth:sanctum'])->group(function () {
        //User Routes
        Route::namespace('User')->name('user.')->group(function () {

            Route::get('/user/profile', 'HomeController@profile')->name('profile');
            Route::post('/isProBonoWork', 'HomeController@isProBonoWork')->name('isProBonoWork');
            Route::post('/user/change/password', 'HomeController@changePassword')->name('change.password');
            Route::post('user/profile/update', 'AccountController@update')->name('profile.update');
            // user primary emiail verify
            Route::post('/user/verify/email', 'AccountController@changeEmailVerify')->name('verify.email');
            Route::post('/user/resend/email/otp', 'AccountController@resendPrimaryEmailOtp')->name('resend.email.otp');

            // Feedback Rating
            Route::post('/postRating', 'AppointmentsController@postRating')->name('postRating');
            Route::post('/postFeedback', 'AppointmentsController@postFeedback')->name('postFeedback');
            
           // Route::post('/UpdateRating', 'RatingController@UpdateRating')->name('UpdateRating');
            //Route::post('/DeleteRating', 'RatingController@DeleteRating')->name('DeleteRating');
            
            // appointment route
            Route::post('/postAppointment', 'AppointmentsController@postAppointment')->name('postAppointment');
            Route::get('/getTherapistAppointment', 'AppointmentsController@getTherapistAppointment')->name('getTherapistAppointment');
            Route::get('/getClientAppointment', 'AppointmentsController@getClientAppointment')->name('getClientAppointment');
        });
    });
});