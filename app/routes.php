<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	return Redirect::to('login');
});

// Not Logged In State
Route::group(array( 'before' => 'guest' ), function()
{
	// Admin
	Route::get('login', 'AdminController@getLogin');
	Route::post('login', 'AdminController@postLogin');
});

// Logged In State
Route::group(array( 'before' => 'auth' ), function()
{
	// Cpanel
	
	// Emergency
	Route::get('cpanel/emergency', 'EmergencyController@getIndex');

	Route::get('cpanel/emergency/type', 'EmergencyController@getIndexType');
	Route::get('cpanel/emergency/type/create', 'EmergencyController@getCreateType');
	Route::post('cpanel/emergency/type/create', 'EmergencyController@postCreateType');
	Route::get('cpanel/emergency/type/{id}/edit', 'EmergencyController@getEditType');
	Route::put('cpanel/emergency/type/{id}/edit', 'EmergencyController@putEditType');
	Route::delete('cpanel/emergency/type/{id}/destroy', 'EmergencyController@deleteDestroyType');
	
	// Fasilitas
	Route::get('cpanel/facility', 'FacilityController@getIndex');
	Route::get('cpanel/facility/create', 'FacilityController@getCreate');
	Route::post('cpanel/facility/create', 'FacilityController@postCreate');
	Route::get('cpanel/facility/{id}/edit', 'FacilityController@getEdit');
	Route::put('cpanel/facility/{id}/edit', 'FacilityController@putEdit');
	Route::delete('cpanel/facility/{id}/destroy', 'FacilityController@deleteDestroy');

	// User
	Route::get('cpanel/user', 'UserController@getIndex');
	
	Route::get('cpanel/user/general', 'UserController@getIndexUser');
	Route::get('cpanel/user/general/create', 'UserController@getCreateUser');
	Route::post('cpanel/user/general/create', 'UserController@postCreateUser');
	Route::get('cpanel/user/general/{id}/edit', 'UserController@getEditUser');
	Route::put('cpanel/user/general/{id}/edit', 'UserController@putEditUser');
	Route::delete('cpanel/user/general/{id}/destroy', 'UserController@deleteDestroyUser');

	Route::get('cpanel/user/ert', 'UserController@getIndexERT');
	Route::get('cpanel/user/ert/create', 'UserController@getCreateERT');
	Route::post('cpanel/user/ert/create', 'UserController@postCreateERT');
	Route::get('cpanel/user/ert/{id}/edit', 'UserController@getEditERT');
	Route::put('cpanel/user/ert/{id}/edit', 'UserController@putEditERT');
	Route::delete('cpanel/user/ert/{id}/destroy', 'UserController@deleteDestroyERT');

	// Logout
	Route::any('logout', 'AdminController@requestLogout');
});
