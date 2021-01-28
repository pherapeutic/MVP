<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();


//connect vendor account
Route::get('/connectwithstrip', 'FrontendController@connectwithstrip');
Route::get('/sendcurltostrip', 'FrontendController@sendcurltostrip');
Route::get('/disConnectTherapistAccount/{stripeConnectId}', 'FrontendController@disConnectTherapistAccount');

// Route::get('/admin/login',function(){
// 	return view('adminlogin');
// });

// Route::get('/emailverification/{token}','HomeController@index')->name('emailverification');
Route::get('/', function(){
	return redirect()->route('admin.home');
})->name('home');


Route::get('privacy','PagesController@privacy')->name('privacy');

Route::middleware('auth')->prefix('admin')->namespace('Admin')->name('admin.')->group(function(){
	//Admin Route
	Route::get('/','HomeController@index')->name('home');
	Route::prefix('user')->namespace('Users')->group(function () {
		Route::resource('client', 'ClientController');
		Route::resource('therapist', 'TherapistController');
	});
	Route::resource('questions', 'QuestionController');
	Route::resource('answers', 'AnswersController');
	Route::resource('faqs', 'FaqsController');
	Route::resource('appointments', 'AppointmentsController');
	Route::resource('therapisttypes', 'TherapistTypeController');
	Route::resource('languages', 'LanguagesController');

	Route::resource('questionnaire', 'QuestionnaireController');	
	Route::post('question-ordering/{order}', 'QuestionnaireController@ordering');

	Route::resource('settings', 'SettingController');

	Route::resource('payments', 'PaymentsController');
	Route::resource('contactus', 'ContactUsController');
	Route::resource('termsandconditions', 'TermsConditionsController');
	Route::resource('privacypolicy', 'PrivacyPolicyController');

    Route::resource('faq', 'FaqController');
	//End Admin Route
});


	// Route::get('/', 'PagesController@index');
	// Route::get('/home','PagesController@index')->name('home');
	//Demo routes
	// Route::get('/datatables', 'PagesController@datatables');
	// Route::get('/ktdatatables', 'PagesController@ktDatatables');
	// Route::get('/select2', 'PagesController@select2');
	// Route::get('/icons/custom-icons', 'PagesController@customIcons');
	// Route::get('/icons/flaticon', 'PagesController@flaticon');
	// Route::get('/icons/fontawesome', 'PagesController@fontawesome');
	// Route::get('/icons/lineawesome', 'PagesController@lineawesome');
	// Route::get('/icons/socicons', 'PagesController@socicons');
	// Route::get('/icons/svg', 'PagesController@svg');

	// // Quick search dummy route to display html elements in search dropdown (header search)
	// Route::get('/quick-search', 'PagesController@quickSearch')->name('quick-search');	

Route::get('/clear-cache', function() {
	\Artisan::call('cache:clear');
	\Artisan::call('config:clear');
	\Artisan::call('config:cache');
	\Artisan::call('view:clear');
	\Artisan::call('route:clear');
	return "Cache cleared";
});