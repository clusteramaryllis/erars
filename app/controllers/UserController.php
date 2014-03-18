<?php

class UserController extends \BaseController {

	protected $gender = array(
		'' => 'Pilih Jenis Kelamin',
		'L' => 'Laki-laki',
		'P' => 'Perempuan'
	);

	protected $orderBy = array(
		'' => 'Urutkan Berdasarkan',
		'asc' => 'Ascending',
		'desc' => 'Descending'
	);

	protected $group = array(
		'' => 'Pilih Grup',
		1 => 'Polisi',
		2 => 'Medis',
		3 => 'Damkar',
		4 => 'Sipil'
	);

	protected $rules = array(
		'no_id' => 'required|min:16|max:16|unique:user,no_id',
		'nama' => 'required|max:50|alpha_space',
		'pass' => 'required|confirmed|max:32',
		'pass_confirmation' => 'required',
		'tmp_lhr' => 'required|alpha|max:15',
		'tgl_lhr' => 'required|date_format:d-m-Y',
		'gender' => 'required|max:1|in:L,P',
		'alamat' => 'required|max:100',
		'pekerjaan' => 'required|max:50|alpha_space',
		'no_hp' => 'required|phone',
		'email' => 'required|max:30|email|unique:user,email'
	);

	protected $facilities = array();

	/**
	 * Konstrukstor
	 */
	public function __construct()
	{
		$this->buildFacilities();
	}

	/**
	 * Tampilan index user umum
	 * @return Response
	 */
	public function getIndexUser()
	{
		$query = DB::table('user');

		// User umum
		$query->where('grup', 4);

		// Ada query
		if (Input::has('q')) {
			$query->where('nama', 'LIKE', '%'. Input::get('q') .'%');
		}

		// Ada urutan
		if (Input::has('order_by')) {
			$query->orderBy('nama', Input::get('order_by'));
		}
		
		$users = $query->paginate(5);

		return View::make('user.index_user', array(
			'users' => $users,
			'orderBy' => $this->orderBy
		));
	}

	/**
	 * Tampilan tambah user umum
	 * @return Response
	 */
	public function getCreateUser()
	{
		return View::make('user.create_user', array('gender' => $this->gender));
	}

	/**
	 * Aksi tambah user umum
	 * @return Redirect
	 */
	public function postCreateUser()
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
			$user = new User;

			$user->no_id = Input::get('no_id');
			$user->nama = Input::get('nama');
			$user->pass = Input::get('pass');
			$user->tmp_lhr = Input::get('tmp_lhr');
			$user->tgl_lhr = date("Y-m-d", strtotime(Input::get('tgl_lhr')));
			$user->gender = Input::get('gender');
			$user->alamat = Input::get('alamat');
			$user->pekerjaan = Input::get('pekerjaan');
			$user->no_hp = Input::get('no_hp');
			$user->email = Input::get('email');

			$user->grup = 4; // default to sipil

			$user->save();

			Session::flash('success_message', 'User <b>'. $user->nama .'</b> berhasil disimpan');
			return Redirect::action('UserController@getIndexUser');
		}
	}

	/**
	 * Tampilan edit user umum
	 * @param  int $id
	 * @return Response
	 */
	public function getEditUser($id)
	{
		$user = User::find($id);

		return View::make('user.edit_user', array(
			'gender' => $this->gender,
			'user' => $user
		));
	}

	/**
	 * Aksi edit user umum
	 * @param  int $id
	 * @return Redirect
	 */
	public function putEditUser($id)
	{
		$rules = $this->rules;
		// without no_id
		unset($rules['no_id']);
		// change email validator
		$rules['email'] = 'required|max:30|email';

		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) 
		{
			return Redirect::back()
				->withErrors($validator)
				->withInput();
		}
		else
		{
			$user = User::find($id);

			$user->nama = Input::get('nama');
			$user->pass = Input::get('pass');
			$user->tmp_lhr = Input::get('tmp_lhr');
			$user->tgl_lhr = date("Y-m-d", strtotime(Input::get('tgl_lhr')));
			$user->gender = Input::get('gender');
			$user->alamat = Input::get('alamat');
			$user->pekerjaan = Input::get('pekerjaan');
			$user->no_hp = Input::get('no_hp');
			$user->email = Input::get('email');

			$user->grup = 4; // default to sipil

			$user->save();

			Session::flash('success_message', 'User <b>'. $user->nama .'</b> berhasil diperbaharui');
			return Redirect::action('UserController@getIndexUser');
		}
	}

	/**
	 * Aksi delete user umum
	 * @param  int $id
	 * @return Redirect
	 */
	public function deleteDestroyUser($id)
	{
		$user = User::find($id);

		$userNama = $user->nama;

		$user->delete();

		Session::flash('delete_message', 'User <b>'. $userNama .'</b> berhasil dihapus');
		return Redirect::back();
	}

	// =============================================================================================
	// | ERT                                                                                       |
	// =============================================================================================
	/**
	 * Tampilan index user ert
	 * @return Response
	 */
	public function getIndexERT()
	{
		// remove sipil
		unset($this->group[4]);

		$query = DB::table('user');

		// User umum
		$query->whereIn('grup', array(1, 2, 3));

		// Ada query
		if (Input::has('q')) {
			$query->where('nama', 'LIKE', '%'. Input::get('q') .'%');
		}

		// Ada grup
		if (Input::has('group_by')) {
			$query->where('grup', Input::get('group_by'));
		}

		// Ada urutan
		if (Input::has('order_by')) {
			$query->orderBy('nama', Input::get('order_by'));
		}
		
		$users = $query->paginate(5);

		return View::make('user.index_ert', array(
			'users' => $users,
			'orderBy' => $this->orderBy,
			'group' => $this->group
		));
	}

	/**
	 * Tampilan tambah user ert
	 * @return Response
	 */
	public function getCreateERT()
	{
		// remove sipil
		unset($this->group[4]);

		$facilities = Facility::all();

		return View::make('user.create_ert', array(
			'gender' => $this->gender,
			'group' => $this->group,
			'facilities' => $facilities
		));
	}

	/**
	 * Aksi tambah user ert
	 * @return Redirect
	 */
	public function postCreateERT()
	{
		$rules = $this->rules;

		// informasi tambahan ERT
		unset($rules['pekerjaan']);

		$rules['tmp_dinas'] = 'required';
		$rules['grup'] = 'required|max:1|in:1,2,3';
		$rules['no_induk'] = 'required|max:30';

		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) 
		{
			return Redirect::back()
				->withErrors($validator)
				->withInput();
		}
		else
		{
			$user = new User;

			$user->no_id = Input::get('no_id');
			$user->nama = Input::get('nama');
			$user->pass = Input::get('pass');
			$user->tmp_lhr = Input::get('tmp_lhr');
			$user->tgl_lhr = date("Y-m-d", strtotime(Input::get('tgl_lhr')));
			$user->gender = Input::get('gender');
			$user->alamat = Input::get('alamat');
			$user->grup = Input::get('grup');
			$user->tmp_dinas = Input::get('tmp_dinas');
			$user->no_induk = Input::get('no_induk');
			$user->no_hp = Input::get('no_hp');
			$user->email = Input::get('email');

			$user->save();

			Session::flash('success_message', 'User <b>'. $user->nama .'</b> berhasil disimpan');
			return Redirect::action('UserController@getIndexERT');
		}
	}

	/**
	 * Tampilan edit user ert
	 * @param  int $id
	 * @return Response
	 */
	public function getEditERT($id)
	{
		// remove sipil
		unset($this->group[4]);

		$user = User::find($id);

		return View::make('user.edit_user', array(
			'gender' => $this->gender,
			'group' => $this->group,
			'user' => $user
		));
	}

	/**
	 * Aksi edit user ert
	 * @param  int $id
	 * @return Redirect
	 */
	public function putEditERT($id)
	{
		$rules = $this->rules;

		// without no_id
		unset($rules['no_id']);
		unset($rules['pekerjaan']);

		$rules['tmp_dinas'] = 'required';
		$rules['grup'] = 'required|max:1|in:1,2,3';
		$rules['no_induk'] = 'required|max:30';
		$rules['email'] = 'required|max:30|email';

		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) 
		{
			return Redirect::back()
				->withErrors($validator)
				->withInput();
		}
		else
		{
			$user = User::find($id);

			$user->nama = Input::get('nama');
			$user->pass = Input::get('pass');
			$user->tmp_lhr = Input::get('tmp_lhr');
			$user->tgl_lhr = date("Y-m-d", strtotime(Input::get('tgl_lhr')));
			$user->gender = Input::get('gender');
			$user->alamat = Input::get('alamat');
			$user->grup = Input::get('grup');
			$user->tmp_dinas = Input::get('tmp_dinas');
			$user->no_induk = Input::get('no_induk');
			$user->pekerjaan = Input::get('pekerjaan');
			$user->no_hp = Input::get('no_hp');
			$user->email = Input::get('email');

			$user->save();

			Session::flash('success_message', 'User <b>'. $user->nama .'</b> berhasil diperbaharui');
			return Redirect::action('UserController@getIndexERT');
		}
	}

	/**
	 * Aksi delete user ert
	 * @param  int $id
	 * @return Redirect
	 */
	public function deleteDestroyERT($id)
	{
		$user = User::find($id);

		$userNama = $user->nama;

		$user->delete();

		Session::flash('delete_message', 'User <b>'. $userNama .'</b> berhasil dihapus');
		return Redirect::back();
	}

	/**
	 * Set fasilitas
	 */
	protected function buildFacilities()
	{
		$facilities = Facility::all();

		$this->facilities[''] = 'Pilih Kantor';

		foreach($facilities as $facility)
		{
			$this->facilities[$facility->gid] = $facility->nama;
		}
	}

}