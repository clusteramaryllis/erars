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

	public function scopeFindByIdPass($query, $id, $pass)
	{
		return $query
			->where('no_id', $id)
			->where('pass', $pass);
	}

	public function scopeGetName($query, $id)
	{
		return $query
			->where('no_id', $id);
	}
}