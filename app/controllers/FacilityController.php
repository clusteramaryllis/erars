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

	}

	/**
	 * Aksi Tambah Fasilitas
	 * @return Redirect
	 */
	public function postCreate()
	{

	}

	/**
	 * Tampilan Edit Fasilitas
	 * @return Response
	 */
	public function getEdit($id)
	{

	}

	/**
	 * Aksi Edit Fasilitas
	 * @return Redirect
	 */
	public function putEdit($id)
	{

	}

	/**
	 * Aksi Delete Fasilitas
	 * @return Redirect
	 */
	public function deleteDestroy($id)
	{

	}

}