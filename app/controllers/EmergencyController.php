<?php

class EmergencyController extends \BaseController {

	protected $orderBy = array(
		'' => 'Pilih Order',
		'asc' => 'Ascending',
		'desc' => 'Descending'
	);

	protected $group = array(
		'' => 'Pilih Grup',
		1 => 'Polisi',
		2 => 'Medis',
		3 => 'Damkar'
	);

	protected $filterBy = array();

	protected $searchBy = array(
		'' => 'Pilih Kolom',
		'type_id' => 'No ID',
		'type_name' => 'Nama Tipe'
	);

	protected $groupType = array(
		'alert_p' => 'Polisi',
		'alert_m' => 'Medis',
		'alert_f' => 'Damkar'
	);

	protected $groupTotal = array(5, 4, 3, 2, 1);
	
	protected $groupName = array(
		5 => 'Admin',
		4 => 'Sipil', 
		3 => 'Damkar', 
		2 => 'Medis', 
		1 => 'Polisi'
	);

	protected $status = array(
		'' => 'Pilih Status',
		0 => 'Palsu',
		1 => 'Valid',
		2 => 'Selesai'
	);

	// validator rules
	protected $rulesType = array(
		'type_name' => 'required|max:20'
	);

	protected $rulesCase = array(
		'type' => 'required|integer',
		'lat' => 'required|coordinate',
		'lon' => 'required|coordinate',
		'desc' => 'required'
	);

	public function getIndex()
	{
		return View::make('emergency.index');
	}

	// =============================================================================================
	// | Emergency                                                                                 |
	// =============================================================================================
	
	/**
	 * Tampilan index emergency
	 * @return Response
	 */
	public function getIndexEmergency()
	{
		$query = EmergencyCase::with(array(
			'em_type',
			'user_reporter',
			'user_validator',
			'user_resolver'
		));

		// Ada query
		if (Input::has('q')) {
			$query->where('desc', 'LIKE', '%'. Input::get('q') .'%');
		}

		// Ada search by
		if (Input::has('filter_by')) {
			$query->where('type', Input::get('filter_by'));
		}

		// Ada urutan
		if (Input::has('order_by')) {
			$query->orderBy('desc', Input::get('order_by'));
		}
		
		$em_cases = $query->paginate(5);

		// Populate filterBy based on emergency type
		$this->filterBy = $this->buildEmergencyType();

		// return $em_cases;

		return View::make('emergency.index_em', array(
			'em_cases' => $em_cases,
			'orderBy' => $this->orderBy,
			'filterBy' => $this->filterBy,
			'status' => $this->status
		));
	}

	/**
	 * Tampilan tambah emergency
	 * @return Response
	 */
	public function getCreateEmergency()
	{
		// Generate Geo-JSON
		$streets = RoadSmg::withGeoJson();

		$em_types = $this->buildEmergencyType();

		return View::make('emergency.create_em', array(
			'em_types' => $em_types,
			'streets' => $streets
		));
	}

	/**
	 * Aksi tambah emergency
	 * @return Redirect
	 */
	public function postCreateEmergency()
	{
		$rules = $this->rulesCase;

		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) 
		{
			return Redirect::back()
				->withErrors($validator)
				->withInput();
		}
		else
		{
			$em_case = new EmergencyCase;

			// get admin user
			$user = DB::table('user')
				->where('grup', 5)
				->first();

			$em_case->type = Input::get('type');
			$em_case->lat = Input::get('lat');
			$em_case->lon = Input::get('lon');
			$em_case->time = DB::raw('NOW()');
			$em_case->reporter = $user->no_id;
			$em_case->validator = $user->no_id;
			$em_case->resolver = $user->no_id;
			$em_case->status = 1; // set to valid
			$em_case->desc = Input::get('desc');

			$em_case->save();

			Session::flash('success_message', 'Emergency dengan No ID <b>'. $em_case->case_id .'</b> berhasil disimpan');
			return Redirect::action('EmergencyController@getIndexEmergency');
		}
	}

	/**
	 * Tampilan edit emergency
	 * @return Response
	 */
	public function getEditEmergency($id)
	{
		$em_case = EmergencyCase::find($id);

		// Generate Geo-JSON
		$streets = RoadSmg::withGeoJson();

		$em_types = $this->buildEmergencyType();

		// User
		$users = DB::table('user')
			->select('no_id', 'nama', 'grup')
			->orderBy('grup', 'desc')
			->get();		
		$users_data = array();

		// filter user by group type
		foreach ($this->groupTotal as $key => $value) {
			foreach ($users as $idx => $user) {
				if ($value === $user->grup) {
					$users_data[$value][] = $user;
					unset($users[$idx]);
				}
			}
		}

		return View::make('emergency.edit_em', array(
			'em_case' => $em_case,
			'em_types' => $em_types,
			'streets' => $streets,
			'users_data' => $users_data,
			'group_name' => $this->groupName,
			'status' => $this->status
		));
	}

	/**
	 * Aksi edit emergency
	 * @return Redirect
	 */
	public function putEditEmergency($id)
	{
		$rules = $this->rulesCase;

		// add edit rules
		$rules['time'] = 'required|date_format:d-m-Y H:i:s';
		$rules['reporter'] = 'required|exists:user,no_id';
		$rules['validator'] = 'required|exists:user,no_id';
		$rules['status'] = 'required|in:0,1,2';

		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) 
		{
			return Redirect::back()
				->withErrors($validator)
				->withInput();
		}
		else
		{
			$em_case = EmergencyCase::find($id);

			$em_case->type = Input::get('type');
			$em_case->lat = Input::get('lat');
			$em_case->lon = Input::get('lon');
			$em_case->time = date("Y-m-d H:i:s", strtotime(Input::get('time')));
			$em_case->reporter = Input::get('reporter');
			$em_case->validator = Input::get('validator');
			$em_case->status = Input::get('status');
			$em_case->desc = Input::get('desc');

			$em_case->save();

			Session::flash('success_message', 'Emergency dengan No ID <b>'. $em_case->case_id .'</b> berhasil diperbaharui');
			return Redirect::action('EmergencyController@getIndexEmergency');
		}
	}

	/**
	 * Aksi delete emergency
	 * @return Redirect
	 */
	public function deleteDestroyEmergency($id)
	{
		$em_case = EmergencyCase::find($id);

		$emCaseID = $em_case->case_id;

		$em_case->delete();

		Session::flash('delete_message', 'Emergency dengan No ID <b>'. $emCaseID .'</b> berhasil dihapus');
		return Redirect::back();
	}

	// =============================================================================================
	// | Tipe Emergency                                                                            |
	// =============================================================================================
	
	/**
	 * Tampilan index tipe emergency
	 * @return Response
	 */
	public function getIndexType()
	{
		$query = DB::table('em_type');

		// Ada query
		if (Input::has('q')) {
			$query->where('type_name', 'LIKE', '%'. Input::get('q') .'%');
		}

		// Ada search by
		if (Input::has('search_by')) {
			$orderName = Input::get('search_by');
		} else {
			$orderName = 'type_id';	
		}

		// Ada urutan
		if (Input::has('order_by')) {
			$query->orderBy($orderName, Input::get('order_by'));
		} else {
			$query->orderBy('type_id', 'asc');
		}
		
		$em_types = $query->paginate(5);

		return View::make('emergency.index_type', array(
			'em_types' => $em_types,
			'groupType' => $this->groupType,
			'orderBy' => $this->orderBy,
			'searchBy' => $this->searchBy
		));
	}

	/**
	 * Tampilan tambah tipe emergency
	 * @return Response
	 */
	public function getCreateType()
	{
		return View::make('emergency.create_type', array(
			'groupType' => $this->groupType
		));
	}

	/**
	 * Aksi tambah tipe emergency
	 * @return Redirect
	 */
	public function postCreateType()
	{
		$rules = $this->rulesType;

		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) 
		{
			return Redirect::back()
				->withErrors($validator)
				->withInput();
		}
		else
		{
			$em_type = new EmergencyType;

			$em_type->type_name = Input::get('type_name');
			if (Input::has('alert_p')) $em_type->alert_p = Input::get('alert_p');
			if (Input::has('alert_m')) $em_type->alert_m = Input::get('alert_m');
			if (Input::has('alert_f')) $em_type->alert_f = Input::get('alert_f');

			$em_type->save();

			Session::flash('success_message', 'Tipe emergency <b>'. $em_type->type_name .'</b> berhasil disimpan');
			return Redirect::action('EmergencyController@getIndexType');
		}
	}

	/**
	 * Tampilan edit tipe emergency
	 * @return Response
	 */
	public function getEditType($id)
	{
		$em_type = EmergencyType::find($id);

		return View::make('emergency.edit_type', array(
			'em_type' => $em_type,
			'groupType' => $this->groupType
		));	
	}

	/**
	 * Aksi edit tipe emergency
	 * @return Redirect
	 */
	public function putEditType($id)
	{
		$rules = $this->rulesType;

		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) 
		{
			return Redirect::back()
				->withErrors($validator)
				->withInput();
		}
		else
		{
			$em_type = EmergencyType::find($id);

			$em_type->type_name = Input::get('type_name');
			if (Input::has('alert_p')) $em_type->alert_p = Input::get('alert_p');
			if (Input::has('alert_m')) $em_type->alert_m = Input::get('alert_m');
			if (Input::has('alert_f')) $em_type->alert_f = Input::get('alert_f');

			$em_type->save();

			Session::flash('success_message', 'Tipe emergency <b>'. $em_type->type_name .'</b> berhasil diperbaharui');
			return Redirect::action('EmergencyController@putIndexType');
		}
	}

	/**
	 * Aksi delete tipe emergency
	 * @return Redirect
	 */
	public function deleteDestroyType($id)
	{
		$em_type = EmergencyType::find($id);

		$emTypeNama = $em_type->type_name;

		$em_type->delete();

		Session::flash('delete_message', 'Tipe emergency <b>'. $emTypeNama .'</b> berhasil dihapus');
		return Redirect::back();
	}

	// =============================================================================================
	// | Statistik                                                                                 |
	// =============================================================================================
	
	/**
	 * Tampilan indeks statistik
	 * @return Response
	 */
	public function getIndexStatistic()
	{
		$query = DB::table('em_case')
			->select(DB::raw('to_char(em_case.time, \'YYYY-MM-DD\') AS date, COUNT(em_case.case_id) AS count'));

		// Ada filter by
		if (Input::has('filter_by')) {
			$query->where('em_case.type', Input::get('filter_by'));
		}

		// Ada interval
		if (Input::has('from')) {
			$query->where( 'em_case.time', '>=', date("Y-m-d", strtotime(Input::get('from'))) . ' 00:00:00');
		}

		if (Input::has('to')) {
			$query->where( 'em_case.time', '<=', date("Y-m-d", strtotime(Input::get('to'))) . ' 23:59:59');
		}

		$query->groupBy('date')
			->orderBy('date', 'desc');
		
		$em_cases = $query->paginate(5);		

		// Populate filterBy based on emergency type
		$this->filterBy = $this->buildEmergencyType();

		return View::make('emergency.index_stats', array(
			'em_cases' => $em_cases,
			'groupType' => $this->groupType,
			'filterBy' => $this->filterBy
		));
	}

	/**
	 * Tampilan charts statistik
	 * @return Response
	 */
	public function getChartStatistic()
	{
		$query = DB::table('em_case')
			->select(DB::raw('to_char(em_case.time, \'YYYY-MM-DD\') AS date, COUNT(em_case.case_id) AS count'));

		// Ada filter by
		if (Input::has('filter_by')) {
			$query->where('em_case.type', Input::get('filter_by'));
		}

		// Ada interval
		if (Input::has('from')) {
			$query->where( 'em_case.time', '>=', date("Y-m-d", strtotime(Input::get('from'))) . ' 00:00:00');
		}

		if (Input::has('to')) {
			$query->where( 'em_case.time', '<=', date("Y-m-d", strtotime(Input::get('to'))) . ' 23:59:59');
		}

		$query->groupBy('date')
			->orderBy('date', 'desc');
		
		$em_cases = $query->get();

		// Populate filterBy based on emergency type
		$this->filterBy = $this->buildEmergencyType();

		return View::make('emergency.chart_stats', array(
			'em_cases' => $em_cases,
			'groupType' => $this->groupType,
			'filterBy' => $this->filterBy
		));	
	}

	/**
	 * Request Ajax untuk Real Time
	 * @return Response
	 */
	public function postAjaxLatestEmergency()
	{
		// no parameter, return error
		if (!Input::has('case_id')) {
			return Response::error('404');
		}

		$case_id = (int) Input::get('case_id');

		while(true) {

			$case = EmergencyCase::with(array(
				'em_type',
				'user_reporter',
				'user_validator',
				'user_resolver'
			))->orderBy('case_id', 'desc')
				->first();

			if ($case && $case->case_id !== $case_id) {
				return $case;
			}

			// delay 5 seconds
			sleep(5);
		}
	}

	/**
	 * Helper Tipe Emergency
	 * @return array
	 */
	protected function buildEmergencyType()
	{
		$em_types = EmergencyType::all();
		$em_types_data = array();

		// Populate for select form
		$em_types_data[''] = 'Pilih Tipe Emergency';
		foreach($em_types as $em_type) {
			$em_types_data[$em_type->type_id] = $em_type->type_name;
		}

		return $em_types_data;
	}
}
