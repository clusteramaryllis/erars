<?php

class HomeController extends \BaseController {

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

}