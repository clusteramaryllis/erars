<?php
use Illuminate\Support\MessageBag;

class AdminController extends \BaseController {

	/**
	 * Tampilan Login
	 * @return Response
	 */
	public function getLogin()
	{
		return View::make('admin.login');
	}

	/**
	 * Aksi Login
	 * @return Redirect
	 */
	public function postLogin()
	{
		$rules = array(
			'username' => 'required',
			'password' => 'required'
		);

		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails())
		{
			return Redirect::back()
				->withErrors($validator)
				->withInput(Input::except('password'));
		}
		else
		{
			$credentials = array(
				'username' => Input::get('username'),
				'password' => Input::get('password')
			);

			if (Auth::attempt($credentials))
			{
				// Berhasil Login
				return Redirect::intended('cpanel');
			}

			// Gagal Login
			$messages = new MessageBag;
			$messages->add('login_failed', 'Username and/or Password Invalid.');

			return Redirect::back()
				->withErrors($messages)
				->withInput(Input::except('password'));
		}
	}

	/**
	 * Aksi Logout
	 * @return Redirect
	 */
	public function requestLogout()
	{
		Auth::logout();
		Session::flush();
		return Redirect::to('login');
	}
}