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

// Route::get('/admin/login',function(){
// 	return view('adminlogin');
// });
Route::get('/','HomeController@index')->name('home');
Route::get('/emailverification/{token}','HomeController@index')->name('emailverification');

Route::group(['middleware' => ['auth']], function(){
	//Admin Route
	Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
		Route::group(['prefix' => 'user','namespace' => 'Users'], function () {
			Route::resource('client', 'ClientController');
			Route::resource('therapist', 'TherapistController');
		});
		Route::resource('questions', 'QuestionController');
		Route::resource('answers', 'AnswersController');
		Route::resource('faqs', 'FaqsController');
		Route::resource('appointments', 'AppointmentsController');
		Route::resource('therapisttypes', 'TherapistTypeController');
		Route::resource('languages', 'LanguagesController');
	});

//End Admin Route


// Route::get('/', 'PagesController@index');
// Route::get('/home','PagesController@index')->name('home');
//Demo routes
Route::get('/datatables', 'PagesController@datatables');
Route::get('/ktdatatables', 'PagesController@ktDatatables');
Route::get('/select2', 'PagesController@select2');
Route::get('/icons/custom-icons', 'PagesController@customIcons');
Route::get('/icons/flaticon', 'PagesController@flaticon');
Route::get('/icons/fontawesome', 'PagesController@fontawesome');
Route::get('/icons/lineawesome', 'PagesController@lineawesome');
Route::get('/icons/socicons', 'PagesController@socicons');
Route::get('/icons/svg', 'PagesController@svg');

// Quick search dummy route to display html elements in search dropdown (header search)
Route::get('/quick-search', 'PagesController@quickSearch')->name('quick-search');	
});
