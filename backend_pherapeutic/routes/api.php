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
            Route::post('/appleLogin', 'LoginController@appleLogin')->name('appleLogin');
            Route::any('/getTermsandConditions', 'TermsandConditionsController@getTermsandConditions')->name('getTermsandConditions');
			
			
            
        });

        // Route::get('/getLanguages', 'LanguageController@getLanguages')->name('getLanguages');
        // Route::get('/getFaqs', 'FaqsController@getFaqs')->name('getFaqs');

        // Route::post('findTherapist', 'User\HomeController@findTherapist')->name('findTherapist');

        // Route::get('/getFeedback', 'User\RatingController@getFeedback')->name('getFeedback');

        //User Routes
        Route::middleware(['auth:sanctum'])->namespace('User')->name('user.')->group(function () {
            Route::get('/logout', 'AccountController@logout')->name('logout');
            Route::get('/user/profile', 'HomeController@profile')->name('profile');
            Route::post('/user/profile/update', 'AccountController@update')->name('profile.update');
            Route::post('/user/profile/update', 'AccountController@update')->name('profile.update');
            Route::post('user/changePassword', 'AccountController@changePassword')->name('changePassword');

            Route::get('user/changeOnlineStatus', 'AccountController@changeOnlineStatus')->name('changeOnlineStatus');
            Route::get('user/changeNotificationStatus', 'AccountController@changeNotificationStatus')->name('changeNotificationStatus');
            //Get Questionare Route
            Route::get('user/questions', 'QuestionareController@getQuestions')->name('getQuestions');
            Route::post('user/answers', 'QuestionareController@postAnswers')->name('postAnswers');
            Route::post('user/clientList', 'TherapistController@clientList')->name('clientList');

            //Search Therapist Route
            Route::post('user/search/therapist', 'TherapistController@searchTherapist')->name('searchTherapist');
            Route::post('user/search/therapistlist', 'TherapistController@searchTherapistList')->name('searchTherapistList');
            Route::post('user/search/therapistlistTest', 'TherapistController@therapistlistTest')->name('therapistlistTest');

            Route::post('/assigned/therapist', 'TherapistController@showAssignedTherapist')->name('showAssignedTherapist');
			
            Route::post('/bonoWorkStatus', 'TherapistController@bonoWorkStatus')->name('therapist.bonoWorkStatus');
			
		

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
            Route::get('/getRating', 'RatingController@getRating')->name('getRating');
            
            // Route::post('/UpdateRating', 'RatingController@UpdateRating')->name('UpdateRating');
            //Route::post('/DeleteRating', 'RatingController@DeleteRating')->name('DeleteRating');
            
            // appointment route
            Route::post('/postAppointment', 'AppointmentsController@postAppointment')->name('postAppointment');
            Route::post('/getTherapistAppointmentRequest', 'AppointmentsController@getTherapistAppointmentRequest')->name('getTherapistAppointmentRequest');
            Route::post('/changeAppointmentStatus', 'AppointmentsController@changeAppointmentStatus')->name('changeAppointmentStatus');

            Route::get('/getTherapistAppointment', 'AppointmentsController@getTherapistAppointment')->name('getTherapistAppointment');
            Route::get('/getClientAppointment', 'AppointmentsController@getClientAppointment')->name('getClientAppointment');
            Route::get('/getTherapistList', 'AppointmentsController@getTherapistList')->name('getTherapistList');
            //payment Routes
            Route::post('/stripeToken', 'PaymentController@stripeToken');

            Route::post('/addUserCard', 'PaymentController@addUserCard')->name('addUserCard');
            Route::get('/getUserCards', 'PaymentController@getUserCards')->name('getUserCards');
            Route::delete('/deleteUserCard', 'PaymentController@deleteUserCard')->name('deleteUserCard');
            Route::post('/createDefaultCard', 'PaymentController@createDefaultCard')->name('createDefaultCard');
            Route::post('/amountHold', 'PaymentController@amountHoldBeforeCall')->name('amountHoldBeforeCall');
            Route::post('/makePayment', 'PaymentController@makePayment')->name('makePayment');
            Route::get('/getPaymentHistory', 'PaymentController@getPaymentHistory')->name('getPaymentHistory');
            Route::get('/stripeData', 'PaymentController@stripeData')->name('stripeData');
            Route::get('/connectWithStripe', 'PaymentController@connectWithStripe')->name('connectWithStripe');

            //Agora authentication token
            Route::post('/agoraToken', 'HomeController@agoraToken')->name('agoraToken');
            Route::post('/agoraTokenRtm', 'HomeController@agoraTokenRtm')->name('agoraTokenRtm');

            //Therapist Notification Routes
            Route::get('/sendVideoCallNotificationToTherapist/{therapistId?}','NotificationController@sendVideoCallNotificationToTherapist');

            //Call logs Route
            Route::post('/createCall', 'PaymentController@createCall')->name('createCall');
            Route::post('/updateCallLog', 'CallLogsController@updateCallLog')->name('updateCallLog');
            Route::get('/getTherapistCallLog', 'CallLogsController@getTherapistCallLog')->name('getTherapistCallLog');
            Route::get('/getClientCallLog', 'CallLogsController@getClientCallLog')->name('getClientCallLog');
            Route::resource('/faq', 'FaqController');
            Route::get('/getAboutUs', 'AboutUsController@getAboutUs')->name('getAboutUs');
            Route::get('/getPrivacyPolicy', 'PrivacyPolicyController@getPrivacyPolicy')->name('getPrivacyPolicy');
            Route::post('/contact-us', 'ContacustController@saveContact');
            
        });
		
		
		 
    });
   	 Route::get('/getQualification', 'Api\User\QualificationController@index')->name('getQualification');
    Route::get('/getLanguages', 'LanguageController@getLanguages')->name('getLanguages');
    Route::get('/getTherapistTypes', 'TherapistTypeController@getTherapistTypes')->name('getTherapistTypes');
});