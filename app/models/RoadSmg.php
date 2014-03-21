<?php

class RoadSmg extends Eloquent {
	
	protected $table = 'roads_smg';

	protected $primaryKey = 'gid';

	public $timestamps = false;

	public function scopeWithGeoJson($query)
	{
		return $query
			->select(DB::raw('gid, street_name, dir, the_geom, source, target, to_cost, r_cost, ST_AsGeoJSON(the_geom) as geo_json'))
			->get();
	}
}