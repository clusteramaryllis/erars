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

	// validator rules
	protected $rulesType = array(
		'type_name' => 'required|max:20'
	);

	public function getIndex()
	{

	}

	// =============================================================================================
	// | Emergency                                                                                 |
	// =============================================================================================
	
	/**
	 * Tampilan index tipe emergency
	 * @return Response
	 */
	public function getIndexEmergency()
	{

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

		Session::flash('delete_message', 'User <b>'. $emTypeNama .'</b> berhasil dihapus');
		return Redirect::back();
	}
}