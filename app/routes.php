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


Route::get('distance', function(){

	set_time_limit(0);

	$time = microtime(true);

	$src_lat = -6.97956178926843;
	$src_lng = 110.401043020797;
	$dest_lat = -6.97277569339086;
	$dest_lng = 110.426216121996;

	$road = new Genetic(
		$src_lat,
		$src_lng,
		$dest_lat,
		$dest_lng
	);

	print_r($road->findBestPath());
	// return Response::json($road->getPopulation());
	
	/*echo '<table class="table">';
	echo '<thead>';
	echo '<tr><td>No</td><td>Jalur</td><td>Jarak Tempuh</td></tr>';
	echo '</thead>';
	echo '<tbody>';
	$pop = $road->getPopulation();
	foreach ($pop as $key => $value) {
		echo '<tr><td>'. ($key+1) .'</td><td>'. implode(" > ", $value['path']) .'</td><td>'. $value['cost'] .'</td></tr>';
	}
	echo '</tbody>';
	echo '</table>';
	echo '<br>';

	$offspring = $road->getOffspring();
	foreach ($offspring as $key => $value) {
		echo 'Generasi ke - '. ($key+1) . '<br>';
		echo '<table class="table">';
		echo '<thead>';
		echo '<tr><td>No</td><td>Jalur</td><td>Jarak Tempuh</td></tr>';
		echo '</thead>';
		echo '<tbody>';
		foreach ($offspring[$key] as $key2 => $value2) {
			echo '<tr><td>'. ($key2+1) .'</td><td>'. implode(" > ", $value2['path']) .'</td><td>'. $value2['cost'] .'</td></tr>';
		}
		echo '</tbody>';
		echo '</table>';
		echo '<br>';
	}
	
	echo 'Time running : '. (microtime(true) - $time) .'s';*/

	/*$pointer = 1;
	$startPos = 43;
	$endPos = 75;

	return DB::table('roads_smg')
						->where('gid', '!=', $pointer)
						->where(function($query) use($startPos, $endPos){
							$query->where('source', $startPos)
								->orWhere('target', $startPos)
								->orWhere('source', $endPos)
								->orWhere('target', $endPos);
						})
						->where(function($query) use($startPos, $endPos){
							$query->whereRaw('NOT(dir = \'FT\' AND (target = ' . $startPos . ' OR target = ' . $endPos . '))')
								->whereRaw('NOT(dir = \'TF\' AND (source = ' . $startPos . ' OR source = ' . $endPos . '))');
						})
						->orderBy(DB::raw('RANDOM()'))
						->toSql();*/
	// return RoadSmg::NearestPoint(1, 6, 10);
	// return RoadSmg::CostBetweenPoint(6, 110, 7, 105);
	// return RoadSmg::NearestCostPointToLine(1, 6, 110, 1);
	// return RoadSmg::NearestEdge(6, 110);
});
/*Route::get('coba', function()
{
	return DB::table('em_case')
		->select(DB::raw('to_char(em_case.time, \'YYYY-MM-DD\') AS date'), 'em_case.type', 'em_type.type_name', DB::raw('COUNT(em_case.case_id)'))
		->join('em_type', 'em_case.type', '=', 'em_type.type_id')
		->groupBy('date', 'em_case.type', 'em_type.type_name')
		->orderBy('date', 'desc')
		->get();
});*/