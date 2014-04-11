<?php

use \Illuminate\Support\Collection;

class HomeController extends \BaseController {

	/**
	 * Tampilan home.
	 * @return response
	 */
	public function getIndex()
	{
		// Generate Geo-JSON
		$streets = RoadSmg::withGeoJson();

		// Facility marker
		$markers = Facility::allWithGeomToLatLng();

		// Cases
		$cases = DB::table('em_case')
			->select(DB::raw('em_type.type_name AS type_name'), DB::raw('COUNT(em_case.case_id) AS count'))
			->where('time', '>=', date('Y-m-d') . ' 00:00:00')
			->where('time', '<=', date('Y-m-d') . ' 23:59:59')
			->join('em_type', 'em_case.type', '=', 'em_type.type_id')
			->groupBy('em_type.type_name')
			->get();

		$em_count = DB::table('em_case')
			->select(DB::raw('COUNT(em_case.case_id) AS count'))
			->where('time', '>=', date('Y-m-d') . ' 00:00:00')
			->where('time', '<=', date('Y-m-d') . ' 23:59:59')
			->first();

		$em_success = DB::table('em_case')
			->select(DB::raw('COUNT(em_case.case_id) AS count'))
			->where('status', '2')
			->where('time', '>=', date('Y-m-d') . ' 00:00:00')
			->where('time', '<=', date('Y-m-d') . ' 23:59:59')
			->first();

		$emergencies = EmergencyCase::with(array(
			'em_type',
			'user_reporter',
			'user_validator',
			'user_resolver'
		))->where('status', '=', '1')
			->get();

		return View::make('home.index', array(
			'cases' => $cases,
			'em_count' => $em_count,
			'em_success' => $em_success,
			'emergencies' => $emergencies,
			'streets' => $streets,
			'markers' => $markers
		));
	}

	/**
	 * Tampilan simulasi
	 * @return response
	 */
	public function getSimulation()
	{
		// Generate Geo-JSON
		$streets = RoadSmg::withGeoJson();

		return View::make('home.simulation', array(
			'streets' => $streets
		));			
	}

	/**
	 * Ajax routing request.
	 * @return response
	 */
	public function postAjaxRouting()
	{

		if ( !Input::has('src_lat') 
			|| !Input::has('src_lng')
			|| !Input::has('dest_lat')
			|| !Input::has('dest_lng'))
		{
			return Response::error('404');	
		}

		try {
			set_time_limit(0);
		} catch(\Exception $e) {}
		

		$road = new Genetic(
			Input::get('src_lat'),
			Input::get('src_lng'),
			Input::get('dest_lat'),
			Input::get('dest_lng')
		);
		$data = $road->findBestPath();

		// dd($data);

		$firstPath = array_shift($data['bestpath']['path']);
		$lastPath = array_pop($data['bestpath']['path']);

		$result = array();

		// starting point distance to roads
		$obj = new \StdClass;
		$obj->gid = '-1';
		$obj->dir = 'FT';
		$obj->street_name = 'Start';
		$obj->geo_json = json_encode(array(
			'type' => 'LineString',
			'coordinates' => array(
				array($data['src_lng'], $data['src_lat']),
				array($data['bestpath']['src_point']['lng'], $data['bestpath']['src_point']['lat'])
			)
		));
		$result[] = $obj;

		// intersection between road from starting point
		$src_part = 1 - $data['bestpath']['src_part'];
		$result[] = ($data['bestpath']['src_dir'] === 0) ?
			RoadSmg::GeoJsonNearestPoint($firstPath, 0, $data['bestpath']['src_part']) :
			RoadSmg::GeoJsonNearestPoint($firstPath, $src_part, 1);
		
		// routing roads
		foreach ($data['bestpath']['path'] as $value) 
		{
			$result[] = RoadSmg::findWithGeoJson($value);
		}

		// intersection between road from end point
		// $dest_part = 1 - $data['bestpath']['dest_part'];
		$result[] = ($data['bestpath']['dest_dir'] === 0) ?
			RoadSmg::GeoJsonNearestPoint($lastPath, 0, $data['bestpath']['dest_part']) :
			RoadSmg::GeoJsonNearestPoint($lastPath, $data['bestpath']['dest_part'], 1);

		// end point distance to roads
		$obj = new \StdClass;
		$obj->gid = '-2';
		$obj->dir = 'FT';
		$obj->street_name = 'Finish';
		$obj->geo_json = json_encode(array(
			'type' => 'LineString',
			'coordinates' => array(
				array($data['dest_lng'], $data['dest_lat']),
				array($data['bestpath']['dest_point']['lng'], $data['bestpath']['dest_point']['lat'])
			)
		));
		$result[] = $obj;

		$collection = new Collection($result);

		return $collection;
	}

	/**
	 * Ajax simulation request.
	 * @return response
	 */
	public function postAjaxSimulation()
	{

		if ( !Input::has('src_lat') 
			|| !Input::has('src_lng')
			|| !Input::has('dest_lat')
			|| !Input::has('dest_lng'))
		{
			return Response::error('404');	
		}

		try {
			set_time_limit(0);
		} catch(\Exception $e) {}
		

		$road = new Genetic(
			Input::get('src_lat'),
			Input::get('src_lng'),
			Input::get('dest_lat'),
			Input::get('dest_lng')
		);
		$data = $road->findBestPath();

		$datas = $data;
		$datas['population'] = $road->getPopulation();
		$datas['offspring'] = $road->getOffspring();

		$firstPath = array_shift($data['bestpath']['path']);
		$lastPath = array_pop($data['bestpath']['path']);

		$result = array();

		// starting point distance to roads
		$obj = new \StdClass;
		$obj->gid = '-1';
		$obj->dir = 'FT';
		$obj->street_name = 'Start';
		$obj->geo_json = json_encode(array(
			'type' => 'LineString',
			'coordinates' => array(
				array($data['src_lng'], $data['src_lat']),
				array($data['bestpath']['src_point']['lng'], $data['bestpath']['src_point']['lat'])
			)
		));
		$result[] = $obj;

		// intersection between road from starting point
		$src_part = 1 - $data['bestpath']['src_part'];
		$result[] = ($data['bestpath']['src_dir'] === 0) ?
			RoadSmg::GeoJsonNearestPoint($firstPath, 0, $data['bestpath']['src_part'])->toArray() :
			RoadSmg::GeoJsonNearestPoint($firstPath, $src_part, 1)->toArray();
		
		// routing roads
		foreach ($data['bestpath']['path'] as $value) 
		{
			$result[] = RoadSmg::findWithGeoJson($value)->toArray();
		}

		// intersection between road from end point
		// $dest_part = 1 - $data['bestpath']['dest_part'];
		$result[] = ($data['bestpath']['dest_dir'] === 0) ?
			RoadSmg::GeoJsonNearestPoint($lastPath, 0, $data['bestpath']['dest_part'])->toArray() :
			RoadSmg::GeoJsonNearestPoint($lastPath, $data['bestpath']['dest_part'], 1)->toArray();

		// end point distance to roads
		$obj = new \StdClass;
		$obj->gid = '-2';
		$obj->dir = 'FT';
		$obj->street_name = 'Finish';
		$obj->geo_json = json_encode(array(
			'type' => 'LineString',
			'coordinates' => array(
				array($data['dest_lng'], $data['dest_lat']),
				array($data['bestpath']['dest_point']['lng'], $data['bestpath']['dest_point']['lat'])
			)
		));
		$result[] = $obj;

		$datas['geojson'] = $result;

		$collection = new Collection($datas);

		return $collection;
	}

}