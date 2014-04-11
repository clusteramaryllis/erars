<?php 

class Genetic {

	/**
	 * Max Generasi
	 */
	const MAX_GEN = 10;

	/**
	 * Max Populasi
	 */
	const MAX_POP = 20;

	/**
	 * Cache cost with direction.
	 * @var array
	 */
	protected $costDirCache = array();

	/**
	 * Response item.
	 * @var array
	 */
	protected $items = array();

	/**
	 * Cache number of roads.
	 * @var int
	 */
	protected $maxRoads;

	/**
	 * Info jalan terdekat.
	 * @var array
	 */
	protected $nearestRoad = array();

	/**
	 * Offspring
	 * @var array
	 */
	protected $offspring = array();

	/**
	 * Info lokasi titik yang dicari dengan jalan.
	 * @var array
	 */
	protected $pointLocation = array();

	/**
	 * Total populasi.
	 * @var mixed
	 */
	protected $population = array();

	/**
	 * Temporary populasi.
	 * @var array
	 */
	protected $popPointer = array();

	/**
	 * Cache cost.
	 * @var array
	 */
	protected $roadCache = array();

	/**
	 * Constructor.
	 * @param int $src_lat
	 * @param int $src_lng
	 * @param int $dest_lat
	 * @param int $dest_lng
	 */
	public function __construct($src_lat, $src_lng, $dest_lat, $dest_lng)
	{
		$this->items['src_lat'] = $src_lat;
		$this->items['src_lng'] = $src_lng;
		$this->items['dest_lat'] = $dest_lat;
		$this->items['dest_lng'] = $dest_lng;

		$this->nearestRoad['src'] = RoadSmg::FindNearestRoad($src_lat, $src_lng);
		$this->nearestRoad['dest'] = RoadSmg::FindNearestRoad($dest_lat, $dest_lng);

		$this->maxRoads = count(RoadSmg::all());
	}

	public function getPopulation()
	{
		return $this->population;
	}

	public function getOffspring()
	{
		return $this->offspring;
	}

	/**
	 * Mencari jalur terbaik
	 * @return array
	 */
	public function findBestPath()
	{
		$this->population = $this->populate();

		$i =0;
		$this->popPointer = $this->population; // first pointer

		while ($i < static::MAX_GEN)
		{
			$this->offspring[] = $this->crossover();
			$this->popPointer = end($this->offspring);

			$i++;
		}

		$this->bestData =  $this->bestPathData();

		return $this->bestData;
	}

	/**
	 * Mencari populasi jalan yang dipakai
	 * @return array
	 */
	protected function populate()
	{
		$this->pointLocation['src'] = RoadSmg::locatePointInRoad(
			$this->nearestRoad['src']->gid, 
			$this->items['src_lat'], 
			$this->items['src_lng']
		)->part;
		$this->pointLocation['dest'] = RoadSmg::locatePointInRoad(
			$this->nearestRoad['dest']->gid, 
			$this->items['dest_lat'], 
			$this->items['dest_lng']
		)->part;
		$this->pointDistance['src'] = RoadSmg::findDistanceToNearestRoad(
			$this->nearestRoad['src']->gid, 
			$this->items['src_lat'],
			$this->items['src_lng']
		)->to_cost;
		$this->pointDistance['dest'] = RoadSmg::findDistanceToNearestRoad(
			$this->nearestRoad['dest']->gid, 
			$this->items['dest_lat'],
			$this->items['dest_lng']
		)->to_cost;

		$diff = $this->pointLocation['dest'] - $this->pointLocation['src'];

		$i = 0;
		$population = array();

		while($i < static::MAX_POP)
		{
			$roadsGroup = $this->generatePossibleRoads($diff); // !!! generate random possible roads ???

			// filter only that direct to destination routes (last array)
			if ($roadsGroup[count($roadsGroup) - 1] === $this->nearestRoad['dest']->gid)
			{
				$population[] = array(
					'path' => $roadsGroup,
					'cost' => $this->getCost($roadsGroup)
				);
				$i++;
			}
		}

		return $population;
	}

	/**
	 * Menghasilkan jalur-jalur secara random yang kemungkinan dipakai
	 * @param  float $diff
	 * @return array
	 */
	protected function generatePossibleRoads($diff)
	{
		$maxRoads = $this->maxRoads;
		$firstTime = true;
		$index = 1;
		// collection of roads id
		$roadsGroup = array($this->nearestRoad['src']->gid); // first road

		$startPos = $this->nearestRoad['src']->source;
		$endPos = $this->nearestRoad['src']->target;

		// FT - FromTo, TF - ToFrom, B - Both, N - Driving not allowed
		if ($this->nearestRoad['src']->dir === "FT")
			$startPos = $endPos;
		else if ($this->nearestRoad['src']->dir === "TF")
			$endPos = $startPos;

		// source and destination on the same road
		if ($this->isSameRoad()
			&& ( ($this->nearestRoad['src']->dir === "FT" && $diff >= 0)
				|| ($this->nearestRoad['src']->dir === "TF" && $diff <= 0)
				|| $this->nearestRoad['src']->dir === "B"
			)
		)
		{
			$roadsGroup[] = $this->nearestRoad['dest']->gid;
		}
		else
		{
			$pointer = $this->nearestRoad['src']->gid;
			$nextPos = 0;

			while ($pointer !== $this->nearestRoad['dest']->gid && $index < $maxRoads)
			{
				if ($firstTime)
				{
					$randomPos = DB::table('roads_smg')
						->where('gid', '!=', $pointer)
						->where(function($query) use($startPos, $endPos)
						{
							$query->where('source', $startPos)
								->orWhere('target', $startPos)
								->orWhere('source', $endPos)
								->orWhere('target', $endPos);
						})
						->where(function($query) use($startPos, $endPos)
						{
							$query->whereRaw('NOT(dir = \'FT\' AND (target = ' . $startPos . ' OR target = ' . $endPos . '))')
								->whereRaw('NOT(dir = \'TF\' AND (source = ' . $startPos . ' OR source = ' . $endPos . '))');
						})
						->orderBy(DB::raw('RANDOM()'))
						->first();

					$firstTime = false;
				}
				else
				{
					$randomPos = DB::table('roads_smg')
						->where('gid', '!=', $pointer)
						->where(function($query) use($nextPos)
						{
							$query->where('source', $nextPos)
								->orWhere('target', $nextPos);
						})
						->where(function($query) use($nextPos)
						{
							$query->whereRaw('NOT(dir = \'FT\' AND target=' . $nextPos . ')')
								->whereRaw('NOT(dir = \'TF\' AND source=' . $nextPos . ')');
						})
						->orderBy(DB::raw('RANDOM()'))
						->first();
				}

				if (!$randomPos) break;

				// assign to next pointer
				$pointer = $randomPos->gid;

				if (!in_array($pointer, $roadsGroup) || $this->isSameRoad())
				{
					$roadsGroup[] = $randomPos->gid;

					if ($randomPos->source === $startPos || $randomPos->source === $endPos)
						$nextPos = $randomPos->target;
					if ($randomPos->target === $startPos || $randomPos->target === $endPos)
						$nextPos = $randomPos->source;

					$startPos = $randomPos->source;
					$endPos = $randomPos->target;
				}

				$index++;
			}
		}

		return $roadsGroup;
	}

	/**
	 * Cari total jarak pada kelompok jalan tertentu.
	 * @param  array $roadsGroup
	 * @return float
	 */
	protected function getCost($roadsGroup)
	{
		$firstPos = array_shift($roadsGroup);
		$endPos = array_pop($roadsGroup);
		$cost = 0;

		if ($roadsGroup)
		{
			foreach ($roadsGroup as $road) 
			{
				// cache
				if (!isset($this->roadCache[$road]))
				{
					$street = RoadSmg::find($road);	
					$this->roadCache[$road] = $street;
				}

				$cost += $this->roadCache[$road]->to_cost;
			}

			if (count($roadsGroup) > 1)
			{
				$srcDir = $this->getDirection($firstPos, array_shift($roadsGroup));
				$destDir = $this->getDirection($endPos, array_pop($roadsGroup));
			}
			else // only 1 member array
			{
				$pos = array_shift($roadsGroup);

				$srcDir = $this->getDirection($firstPos, $pos);
				$destDir = $this->getDirection($endPos, $pos);
			}

			// caching to improve performance
			if (!isset($this->costDirCache[$this->nearestRoad['src']->gid][$srcDir]))
			{
				$this->costDirCache[$this->nearestRoad['src']->gid] = array();
				$this->costDirCache[$this->nearestRoad['src']->gid][$srcDir] = RoadSmg::findDistanceToNearestDirectedRoad(
					$this->nearestRoad['src']->gid, 
					$this->items['src_lat'],
					$this->items['src_lng'],
					$srcDir
				)->to_cost;
			}
			$cost += $this->costDirCache[$this->nearestRoad['src']->gid][$srcDir];

			if (!isset($this->costDirCache[$this->nearestRoad['dest']->gid][$destDir]))
			{
				$this->costDirCache[$this->nearestRoad['dest']->gid] = array();
				$this->costDirCache[$this->nearestRoad['dest']->gid][$destDir] = RoadSmg::findDistanceToNearestDirectedRoad(
					$this->nearestRoad['dest']->gid, 
					$this->items['dest_lat'],
					$this->items['dest_lng'],
					$srcDir
				)->to_cost;
			}
			$cost += $this->costDirCache[$this->nearestRoad['dest']->gid][$destDir];

			$cost += $this->pointDistance['src'];
			$cost += $this->pointDistance['dest'];
		}

		if ($this->isSameRoad()) // same road
		{
			$cost += $this->getDistanceBetweenSameRoad();
		}

		return $cost;
	}

	/**
	 * Cari total jarak pada jalan yang sama.
	 * @return float
	 */
	protected function getDistanceBetweenSameRoad()
	{
		return (abs($this->pointLocation['src'] - $this->pointLocation['dest']) * $this->nearestRoad['src']->gid);
	}

	/**
	 * Mencari kode arah.
	 * @return int
	 */
	protected function getDirection($road1, $road2)
	{
		// cache
		if (!isset($this->roadCache[$road1]))
		{
			$street1 = RoadSmg::find($road1);	
			$this->roadCache[$road1] = $street1;
		}
		if (!isset($this->roadCache[$road2]))
		{
			$street2 = RoadSmg::find($road2);	
			$this->roadCache[$road2] = $street2;
		}
		$src = $this->roadCache[$road1];
		$dest = $this->roadCache[$road2];

		if ($src->source === $dest->source || $src->source === $dest->target)
			return 0;
		else if ($src->target === $dest->source || $src->target === $dest->target)
			return 1;
		else
			return -1;
	}

	/**
	 * Rumus persilangan 2 gen.
	 * @return array
	 */
	protected function crossover()
	{
		$bestParents = $this->getBestParents($this->popPointer);
		$newPopulation = array_slice($bestParents, 0);

		$start = 2;
		$limit = static::MAX_POP - 1;

		while ($start < $limit) 
		{
			$randomParent1 = rand(0, $limit);
			$randomParent2 = rand_without(0, $limit, array($randomParent1));

			$firstPath = 0;
			$lastPath = 0;
			// compare value in first index
			if (reset($this->popPointer[$randomParent1]['path']) === reset($this->popPointer[$randomParent2]['path']))
				$firstPath++;
			// compare value in last index
			if (end($this->popPointer[$randomParent1]['path']) === end($this->popPointer[$randomParent2]['path']))
				$lastPath++;

			$index = $firstPath;
			$path1Pos = 0;
			$path2Pos = 0;

			while (($index < count($this->popPointer[$randomParent1]['path']) - ($lastPath + 1)) 
				&& ($index < count($this->popPointer[$randomParent2]['path']) - ($lastPath + 1))
			)
			{
				if ( $firstPath >= count($this->popPointer[$randomParent1]['path']) 
					|| $firstPath >= count($this->popPointer[$randomParent2]['path']) 
				)
				{
					break;
				}
				
				if (in_array($this->popPointer[$randomParent1]['path'][$index], $this->popPointer[$randomParent2]['path']))
				{
					$path1Pos = $index;
					$path2Pos = array_search(
						$this->popPointer[$randomParent1]['path'][$index], 
						$this->popPointer[$randomParent2]['path']
					);
				}
				$index++;
			}

			$crossover = !isset($path1Pos) || ($path2Pos <= $path1Pos) ? false : true;

			if ($crossover)
			{
				// divided the array
				$parent1Group1 = array_slice($this->popPointer[$randomParent1]['path'], 0, $path1Pos + 1);
				$parent1Group2 = array_slice($this->popPointer[$randomParent1]['path'], $path1Pos + 1);
				$parent2Group1 = array_slice($this->popPointer[$randomParent2]['path'], 0, $path2Pos + 1);
				$parent2Group2 = array_slice($this->popPointer[$randomParent2]['path'], $path2Pos + 1);

				$dataGroup1 = array_merge($parent1Group1, $parent2Group2);
				$dataGroup2 = array_merge($parent2Group1, $parent1Group2);

				$newPopulation[] = array(
					'path' => $dataGroup1,
					'cost' => $this->getCost($dataGroup1),
					'parent1' => $randomParent1,
					'parent2' => $randomParent2
				);
				$newPopulation[] = array(
					'path' => $dataGroup2,
					'cost' => $this->getCost($dataGroup2),
					'parent1' => $randomParent1,
					'parent2' => $randomParent2
				);
			}
			else
			{
				$newPopulation[] = array(
					'path' => $this->popPointer[$randomParent1]['path'],
					'cost' => $this->getCost($this->popPointer[$randomParent1]['path']),
					'parent1' => $randomParent1,
					'parent2' => $randomParent2
				);
				$newPopulation[] = array(
					'path' => $this->popPointer[$randomParent2]['path'],
					'cost' => $this->getCost($this->popPointer[$randomParent2]['path']),
					'parent1' => $randomParent1,
					'parent2' => $randomParent2
				);	
			}

			$start += 2;
		}

		return $newPopulation;
	}

	/**
	 * Cari gen orang tua terbaik.
	 * @param  array $population
	 * @return array
	 */
	protected function getBestParents($population)
	{
		// sort by minimum cost
		usort($population, function($a, $b)
		{
			return $a['cost'] - $b['cost'];
		});

		// take the 2 of most minimum cost
		$bestParents[] = reset($population);
		$bestParents[] = next($population);

		foreach ($bestParents as $key => $value) 
		{
			$bestParents[$key]['parent1'] = '';
			$bestParents[$key]['parent2'] = '';
		}

		return $bestParents;
	}

	/**
	 * Check if source and destination on the same road.
	 * @return boolean
	 */
	protected function isSameRoad()
	{
		return $this->nearestRoad['src']->gid === $this->nearestRoad['dest']->gid;
	}

	/**
	 * Get Best Path data.
	 * @return array
	 */
	protected function bestPathData()
	{
		$finalRoads = array_slice($this->popPointer, 0);

		usort($finalRoads, function($a, $b){
			return $a['cost'] - $b['cost'];
		});

		reset($finalRoads);
		$key= key($finalRoads);
		$path = $finalRoads[$key]['path'];

		$firstPath = reset($path);
		$afterFirstPath = next($path);
		$lastPath = end($path);
		$beforeLastPath = prev($path);

		$data = array();

		$data['src_lat'] = $this->items['src_lat'];
		$data['src_lng'] = $this->items['src_lng'];
		$data['dest_lat'] = $this->items['dest_lat'];
		$data['dest_lng'] = $this->items['dest_lng'];
		$data['bestpath'] = array(
			'path' => $path,
			'cost' => $finalRoads[$key]['cost'],
			'src_part' => $this->pointLocation['src'],
			'dest_part' => $this->pointLocation['dest'],
			'src_point' => RoadSmg::FindNearestPoint(
				$this->nearestRoad['src']->gid, 
				$this->items['src_lat'],
				$this->items['src_lng']
			),
			'dest_point' => RoadSmg::FindNearestPoint(
				$this->nearestRoad['dest']->gid, 
				$this->items['dest_lat'],
				$this->items['dest_lng']
			),
			'src_dir' => $this->getDirection(
				reset($path), 
				next($path)
			),
			'dest_dir' => $this->getDirection(
				end($path),
				prev($path)
			)
		);

		return $data;
	}

}
