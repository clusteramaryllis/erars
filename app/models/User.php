<?php

class User extends Eloquent {
	protected $table = 'user';

	protected $primaryKey = 'no_id';

	public $incrementing = false;

	// protected $hidden = array('pass');

	public $timestamps = false;

	// relasi
	public function facility()
	{
		return $this->belongsTo('Facility', 'tmp_dinas');
	}
}