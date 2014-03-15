<?php

class User extends Eloquent {
	protected $table = 'user';

	protected $primaryKey = 'no_id';

	protected $hidden = array('pass');

	public $timestamps = false;
}