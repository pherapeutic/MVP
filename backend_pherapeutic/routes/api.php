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

Route::prefix('v1')->group(function () {
    
    Route::namespace('Api')->group(function () {
        // Authentication Routes
        Route::namespace('Auth')->name('user.')->group(function () {
            Route::post('/login', 'LoginController@login')->name('login');
            // Route::post('logout', 'User\HomeController@logout')->name('logout');
            Route::post('/register', 'RegisterController@register')->name('register');
            Route::post('/verifyOtp', 'RegisterController@verifyOtp')->name('verifyOtp');
            Route::post('/resendOtp', 'RegisterController@resendOtp')->name('resendOtp');
            Route::post('/forgotPassword', 'LoginController@forgotPassword')->name('forgotPassword');
            Route::post('/resetPassword', 'LoginController@resetPassword')->name('resetPassword');
            Route::post('/socialLogin', 'LoginController@socialLogin')->name('socialLogin');
        });

        // Route::get('/getLanguages', 'LanguageController@getLanguages')->name('getLanguages');
        // Route::get('/getFaqs', 'FaqsController@getFaqs')->name('getFaqs');

        // Route::post('findTherapist', 'User\HomeController@findTherapist')->name('findTherapist');

        // Route::get('/getRating', 'User\RatingController@getRating')->name('getRating');
        // Route::get('/getFeedback', 'User\RatingController@getFeedback')->name('getFeedback');

        //User Routes
        Route::middleware(['auth:sanctum'])->namespace('User')->name('user.')->group(function () {
            Route::get('/logout', 'AccountController@logout')->name('logout');
            Route::get('/user/profile', 'HomeController@profile')->name('profile');
            Route::post('/user/profile/update', 'AccountController@update')->name('profile.update');
            Route::post('user/changePassword', 'AccountController@changePassword')->name('changePassword');

            Route::get('user/changeOnlineStatus', 'AccountController@changeOnlineStatus')->name('changeOnlineStatus');
            Route::get('user/changeNotificationStatus', 'AccountController@changeNotificationStatus')->name('changeNotificationStatus');
            //Get Questionare Route
            Route::get('user/questions', 'QuestionareController@getQuestions')->name('getQuestions');
            Route::post('user/answers', 'QuestionareController@postAnswers')->name('postAnswers');
            //Search Therapist Route
            Route::post('user/search/therapist', 'TherapistController@searchTherapist')->name('searchTherapist');
            Route::post('/assigned/therapist', 'TherapistController@showAssignedTherapist')->name('showAssignedTherapist');

            // Route::post('/isProBonoWork', 'HomeController@isProBonoWork')->name('isProBonoWork');
            // Route::post('/user/change/password', 'HomeController@changePassword')->name('change.password');
            
            // user primary emiail verify
            // Route::post('/user/verify/email', 'AccountController@changeEmailVerify')->name('verify.email');
            // Route::post('/user/resend/email/otp', 'AccountController@resendPrimaryEmailOtp')->name('resend.email.otp');

            // Feedback Rating
            // Route::post('/postRating', 'AppointmentsController@postRating')->name('postRating');
            // Route::post('/postFeedback', 'AppointmentsController@postFeedback')->name('postFeedback');

            Route::post('/clientPostRating', 'RatingController@clientPostRating')->name('clientPostRating');
            Route::post('/therapistPostFeedback', 'RatingController@therapistPostFeedback')->name('therapistPostFeedback');
            
            // Route::post('/UpdateRating', 'RatingController@UpdateRating')->name('UpdateRating');
            //Route::post('/DeleteRating', 'RatingController@DeleteRating')->name('DeleteRating');
            
            // appointment route
            Route::post('/postAppointment', 'AppointmentsController@postAppointment')->name('postAppointment');
            Route::post('/getTherapistAppointmentRequest', 'AppointmentsController@getTherapistAppointmentRequest')->name('getTherapistAppointmentRequest');
            Route::post('/changeAppointmentStatus', 'AppointmentsController@changeAppointmentStatus')->name('changeAppointmentStatus');

            Route::get('/getTherapistAppointment', 'AppointmentsController@getTherapistAppointment')->name('getTherapistAppointment');
            Route::get('/getClientAppointment', 'AppointmentsController@getClientAppointment')->name('getClientAppointment');

            //payment Routes
            Route::post('/stripeToken', 'PaymentController@stripeToken');

            Route::post('/addUserCard', 'PaymentController@addUserCard')->name('addUserCard');
            Route::get('/getUserCards', 'PaymentController@getUserCards')->name('getUserCards');
            Route::delete('/deleteUserCard', 'PaymentController@deleteUserCard')->name('deleteUserCard');
            Route::post('/createDefaultCard', 'PaymentController@createDefaultCard')->name('createDefaultCard');
            Route::post('/makePayment', 'PaymentController@makePayment')->name('makePayment');

            //Therapist Notification Routes
            Route::get('/sendVideoCallNotificationToTherapist/{therapistId?}','NotificationController@sendVideoCallNotificationToTherapist');            

        });
    });

    Route::get('/getLanguages', 'LanguageController@getLanguages')->name('getLanguages');
    Route::get('/getTherapistTypes', 'TherapistTypeController@getTherapistTypes')->name('getTherapistTypes');
});