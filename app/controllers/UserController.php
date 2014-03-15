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

	/**
	 * Tampilan index user umum
	 * @return Response
	 */
	public function getIndexUser()
	{
		$query = DB::table('user');

		// User umum
		$query->whereNull('grup');

		// Ada query
		if (Input::has('q')) {
			$query->where('nama', 'LIKE', '%'. Input::get('q').'%');

			// Ada urutan
			if (Input::has('order_by')) {
				$query->orderBy('nama', Input::get('order_by'));
			}
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
		$rules = array(
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

			$user->save();

			Session::flash('success_message', 'User <b>'. $user->nama .'</b> berhasil disimpan');
			return Redirect::action('UserController@getIndexUser');
		}
	}

}