<?php

class EmergencyCase extends Eloquent {
	
	protected $table = 'em_case';

	protected $primaryKey = 'case_id';

	public $timestamps = false;

	public function em_type()
	{
		return $this->belongsTo('EmergencyType', 'type');
	}

	public function user_reporter()
	{
		return $this->belongsTo('User', 'reporter');	
	}

	public function user_validator()
	{
		return $this->belongsTo('User', 'validator');	
	}

	public function user_resolver()
	{
		return $this->belongsTo('User', 'resolver');
	}
}