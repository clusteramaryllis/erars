<?php

class RoadSmg extends Eloquent {
	
	protected $table = 'roads_smg';

	protected $primaryKey = 'gid';

	public $timestamps = false;

	public function scopeWithGeoJson($query)
	{
		return $query
			->select(DB::raw('gid, street_name, dir, the_geom, source, target, to_cost, r_cost, ST_AsGeoJSON(the_geom) AS geo_json'))
			->get();
	}

	public function scopeFindWithGeoJson($query, $id)
	{
		return $query
			->select(DB::raw('gid, street_name, dir, the_geom, source, target, to_cost, r_cost, ST_AsGeoJSON(the_geom) AS geo_json'))
			->where('gid', $id)
			->first();
	}

	public function scopeFindNearestRoad($query, $lat, $lng)
	{
		return $query
			->select(DB::raw('*, ST_Distance(ST_GeomFromText(\'POINT(' . $lng . ' ' . $lat . ')\'), the_geom) AS dist'))
			->orderBy('dist', 'asc')
			->first();
	}

	public function scopeFindNearestPoint($query, $id, $lat, $lng)
	{
		$query1 = clone $query;

		return $query1
			->select(DB::raw('ST_Y(ST_ClosestPoint(the_geom, ST_GeomFromText(\'POINT(' . $lng . ' ' . $lat . ')\'))) AS lat, ST_X(ST_ClosestPoint(the_geom, ST_GeomFromText(\'POINT(' . $lng . ' ' . $lat . ')\'))) AS lng'))
			->from(DB::raw('(SELECT the_geom FROM roads_smg WHERE gid=' . $id . ') AS roads'))
			->first()
			->toArray();
	}

	/**
	 * Find the distance/cost from nearest roads with directed information.
	 * @param  resource $query
	 * @param  int $id
	 * @param  int $lat
	 * @param  int $lng
	 * @param  int $direction
	 * @return float
	 */
	public function scopeFindDistanceToNearestDirectedRoad($query, $id, $lat, $lng, $direction)
	{
		$partQuery = $this->scopeLocatePointInRoad($query, $id, $lat, $lng);

		if ($direction != 0) 
			$part = (1 - $partQuery->part);
		else
			$part = $partQuery->part;

		return $query
			->select(DB::raw('to_cost*' . $part . ' AS to_cost'))
			->where('gid', $id)
			->first();
	}

	/**
	 * Locate point loacation in road
	 * @param  resource $query
	 * @param  $id 
	 * @param  $lat
	 * @param  $lng
	 * @return float
	 */
	public function scopeLocatePointInRoad($query, $id, $lat, $lng)
	{
		$query1 = clone $query;

		return $query1
			->select(DB::raw('ST_Line_Locate_Point(the_geom, ST_GeomFromText(\'POINT(' . $lng . ' ' . $lat . ')\')) AS part'))
			->from(DB::raw('(SELECT the_geom FROM roads_smg WHERE gid=' . $id . ') AS roads'))
			->first();
	}

	/**
	 * Find the distance specific point to nearest road
	 * @param  resource $query
	 * @param  $id 
	 * @param  $lat
	 * @param  $lng
	 * @return float
	 */
	public function scopeFindDistanceToNearestRoad($query, $id, $lat, $lng)
	{
		$query1 = clone $query;

		return $query1
			->select(DB::raw('ST_Distance(the_geom, ST_GeomFromText(\'POINT(' . $lng . ' ' . $lat . ')\')) AS cost'))
			->from(DB::raw('(SELECT the_geom FROM roads_smg WHERE gid=' . $id . ') AS roads'))
			->first();
	}

	public function scopeCostBetweenPoint($query, $lat1, $lng1, $lat2, $lng2)
	{
		$result = $query->
			select(DB::raw('ST_Distance(
				ST_GeomFromText(\'POINT(' . $lng1 . ' ' . $lat1 . ')\'), 
				ST_GeomFromText(\'POINT(' . $lng2 . ' ' . $lat2 . ')\')
			) AS cost'))->first();

		return $result ? $result->cost : null;
	}

	public function scopeGeoJsonNearestPoint($query, $id, $start, $end)
	{
		$query1 = clone $query;

		return $query1
			->select(DB::raw('gid, street_name, dir, source, target, to_cost, r_cost, ST_AsGeoJSON(ST_Line_Substring(the_geom, ' . $start . ', ' . $end . ')) AS geo_json'))
			->from(DB::raw('(SELECT gid, street_name, dir, the_geom, source, target, to_cost, r_cost FROM roads_smg WHERE gid=' . $id . ') AS roads'))
			->first();
	}
}