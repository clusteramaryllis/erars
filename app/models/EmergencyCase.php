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

	public function scopeGetActivebackup($query)
	{
		return $query
			->select(DB::raw('*'))
			->where('status',1)
			->orwhere('status',NULL)
			->orderBy('time', 'desc')
			->get();
	}

	public function scopeGetActive($query)
	{
		return $query
			->select(DB::raw('*'))
			->whereNull('status')
			->orWhere('status', 1)
			->join('em_type', function($join){
				$join->on('em_case.type', '=', 'em_type.type_id')
				->where('em_type.alert_p', '=', 1);
			})
			->get();
	}
}