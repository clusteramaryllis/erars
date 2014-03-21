<?php

class FacilityController extends \BaseController {

	protected $orderBy = array(
		'' => 'Urutkan Berdasarkan',
		'asc' => 'Ascending',
		'desc' => 'Descending'
	);

	protected $group = array(
		'' => 'Pilih Tipe',
		'P' => 'Polisi',
		'M' => 'Medis',
		'F' => 'Damkar'
	);

	protected $rules = array(
		'nama' => 'required|max:30|alpha_space',
		'type' => 'required|max:1|in:P,M,F',
		'alamat' => 'required|max:50',
		'telp' => 'required|max:20|phone',
		'lat' => 'required|coordinate',
		'lng' => 'required|coordinate'
	);

	protected $srid = 0;

	/**
	 * Tampilan Indeks Fasilitas
	 * @return Response
	 */
	public function getIndex()
	{
		$query = DB::table('em_facility');

		// Ada query
		if (Input::has('q')) {
			$query->where('nama', 'LIKE', '%'. Input::get('q') .'%');
		}

		// Ada grup
		if (Input::has('group_by')) {
			$query->where('type', Input::get('group_by'));
		}

		// Ada urutan
		if (Input::has('order_by')) {
			$query->orderBy('nama', Input::get('order_by'));
		}
		
		$facilities = $query->paginate(5);

		return View::make('facility.index', array(
			'facilities' => $facilities,
			'orderBy' => $this->orderBy,
			'group' => $this->group
		));
	}

	/**
	 * Tampilan Tambah Fasilitas
	 * @return Response
	 */
	public function getCreate()
	{
		// Generate Geo-JSON
		$streets = RoadSmg::withGeoJson();

		return View::make('facility.create', array(
			'type' => $this->group,
			'streets' => $streets
		));
	}

	/**
	 * Aksi Tambah Fasilitas
	 * @return Redirect
	 */
	public function postCreate()
	{
		$rules = $this->rules;

		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) 
		{
			return Redirect::back()
				->withErrors($validator)
				->withInput();
		}
		else
		{
			$facility = new Facility;

			$facility->nama = Input::get('nama');
			$facility->type = Input::get('type');
			$facility->alamat = Input::get('alamat');
			$facility->telp = Input::get('telp');
			$facility->the_geom = DB::raw('ST_SetSRID(ST_MakePoint(' . Input::get('lng') . ',' . Input::get('lat') . '), ' . $this->srid . ')');

			$facility->save();

			Session::flash('success_message', 'Fasilitas <b>'. $facility->nama .'</b> berhasil disimpan');
			return Redirect::action('FacilityController@getIndex');
		}
	}

	/**
	 * Tampilan Edit Fasilitas
	 * @return Response
	 */
	public function getEdit($id)
	{
		// Generate Geo-JSON
		$streets = RoadSmg::withGeoJson();

		// Converts geometry to latitude or longitude
		$facility = Facility::findWithGeomToLatLng($id);
			

		return View::make('facility.edit', array(
			'type' => $this->group,
			'streets' => $streets,
			'facility' => $facility
		));
	}

	/**
	 * Aksi Edit Fasilitas
	 * @return Redirect
	 */
	public function putEdit($id)
	{
		$rules = $this->rules;

		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) 
		{
			return Redirect::back()
				->withErrors($validator)
				->withInput();
		}
		else
		{
			$facility = Facility::find($id);

			$facility->nama = Input::get('nama');
			$facility->type = Input::get('type');
			$facility->alamat = Input::get('alamat');
			$facility->telp = Input::get('telp');
			$facility->the_geom = DB::raw('ST_SetSRID(ST_MakePoint(' . Input::get('lng') . ',' . Input::get('lat') . '), ' . $this->srid . ')');

			$facility->save();

			Session::flash('success_message', 'Fasilitas <b>'. $facility->nama .'</b> berhasil diperbaharui');
			return Redirect::action('FacilityController@getIndex');
		}
	}

	/**
	 * Aksi Delete Fasilitas
	 * @return Redirect
	 */
	public function deleteDestroy($id)
	{
		$facility = Facility::find($id);

		$facilityNama = $facility->nama;

		$facility->delete();

		Session::flash('delete_message', 'Fasilitas <b>'. $facilityNama .'</b> berhasil dihapus');
		return Redirect::back();
	}

}