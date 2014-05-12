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
	Route::get('cpanel', 'HomeController@getIndex');
	Route::get('cpanel/simulation', 'HomeController@getSimulation');

	// Ajax Request
	Route::post('cpanel/routing', array( 'before' => 'ajax', 'uses' => 'HomeController@postAjaxRouting'));
	Route::post('cpanel/simulation/routing', array( 'before' => 'ajax', 'uses' => 'HomeController@postAjaxSimulation'));

	// Emergency
	Route::get('cpanel/emergency', 'EmergencyController@getIndex');

	Route::get('cpanel/emergency/case', 'EmergencyController@getIndexEmergency');
	Route::get('cpanel/emergency/case/create', 'EmergencyController@getCreateEmergency');
	Route::post('cpanel/emergency/case/create', 'EmergencyController@postCreateEmergency');
	Route::get('cpanel/emergency/case/{id}/edit', 'EmergencyController@getEditEmergency');
	Route::put('cpanel/emergency/case/{id}/edit', 'EmergencyController@putEditEmergency');
	Route::delete('cpanel/emergency/case/{id}/destroy', 'EmergencyController@deleteDestroyEmergency');

	Route::get('cpanel/emergency/type', 'EmergencyController@getIndexType');
	Route::get('cpanel/emergency/type/create', 'EmergencyController@getCreateType');
	Route::post('cpanel/emergency/type/create', 'EmergencyController@postCreateType');
	Route::get('cpanel/emergency/type/{id}/edit', 'EmergencyController@getEditType');
	Route::put('cpanel/emergency/type/{id}/edit', 'EmergencyController@putEditType');
	Route::delete('cpanel/emergency/type/{id}/destroy', 'EmergencyController@deleteDestroyType');

	Route::get('cpanel/emergency/statistic', 'EmergencyController@getIndexStatistic');
	Route::get('cpanel/emergency/statistic/chart', 'EmergencyController@getChartStatistic');

	// Ajax Request
	Route::post('cpanel/emergency/ajax/latest', array( 'before' => 'ajax', 'uses' => 'EmergencyController@postAjaxLatestEmergency'));
	
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

// XML
Route::get('mobile/login/{id}/{pass}/{grup}', 'MobileController@getLogin');
Route::get('mobile/user/register/{id}/{pass}/{nama}/{tmp_lahir}/{tgl}/{bln}/{thn}/{gender}/{alamat}/{kerja}/{telp}/{email}', 'MobileController@postUserRegister');
Route::get('mobile/user/update/{id}/{nama}/{tmp_lahir}/{tgl}/{bln}/{thn}/{gender}/{alamat}/{kerja}/{telp}/{email}', 'MobileController@putUserUpdate');
Route::get('mobile/user/chgpwd/{id}/{old_pass}/{new_pass}', 'MobileController@getChangePassword');
Route::get('mobile/user/verify/{id}', 'MobileController@getVerifyUser');
Route::get('mobile/emergency/get', 'MobileController@getEM');
Route::get('mobile/emergency/send/{id}/{type}/{lng}/{lat}/{desc}/{user_type}', 'MobileController@postEmergencySend');
Route::get('mobile/emergency/validate/{user_id}/{em_id}', 'MobileController@setEmergencyValid');
Route::get('mobile/emergency/fake/{user_id}/{em_id}', 'MobileController@setEmergencyFake');
Route::get('mobile/emergency/resolve/{user_id}/{em_id}', 'MobileController@setEmergencyResolved');
Route::get('mobile/roads', 'MobileController@getRoads');
Route::get('mobile/routes/{src_lng}/{src_lat}/{dest_lng}/{dest_lat}', 'MobileController@getRoutes');
Route::get('coba', function(){
	return EmergencyCase::getActive();
});