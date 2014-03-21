<?php

class Facility extends Eloquent {
	
	protected $table = 'em_facility';

	protected $primaryKey = 'gid';

	public $timestamps = false;

	public function scopeFindWithGeomToLatLng($query, $id)
	{
		return $query
			->select(DB::raw('gid, nama, type, alamat, telp, ST_Y(the_geom) as lat, ST_X(the_geom) as lng'))
			->where('gid', $id)
			->first();
	}
}