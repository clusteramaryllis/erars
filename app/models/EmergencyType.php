<?php

class EmergencyType extends Eloquent {
	
	protected $table = 'em_type';

	protected $primaryKey = 'type_id';

	public $timestamps = false;
}