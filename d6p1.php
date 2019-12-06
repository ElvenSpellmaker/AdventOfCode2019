<?php

$orbitStrings = explode("\n", rtrim(file_get_contents('d6.txt')));

class Orbit
{
	private $orbitName;

	private $parentOrbit;

	private $orbitSum;

	private $orbits;

	function __construct(string $orbitName, ?string $parentOrbit, array &$orbits)
	{
		$this->orbitName = $orbitName;
		$this->parentOrbit = $parentOrbit;
		$this->orbits = &$orbits;
	}

	public function getOrbitSum() : int
	{
		if (isset($this->orbitSum))
		{
			return $this->orbitSum;
		}

		if ($this->parentOrbit !== null)
		{
			$this->orbitSum = $this->orbits[$this->parentOrbit]->getOrbitSum() + 1;
			return $this->orbitSum;
		}

		return 0;
	}

	public function __debugInfo()
	{
		return [
			'orbitName' => $this->orbitName,
			'parentOrbit' => $this->parentOrbit,
		];
	}
}

$orbits = [];
$com = new Orbit('COM', null, $orbits);
$orbits['COM'] = $com;

foreach ($orbitStrings as $orbit)
{
	preg_match('%(.+)\)(.*)%', $orbit, $matches);
	[, $orbitee, $orbiter] = $matches;

	$orbit = new Orbit($orbiter, $orbitee, $orbits);
	$orbits[$orbiter] = $orbit;
}

$sum = 0;
foreach ($orbits as $orbit)
{
	$sum += $orbit->getOrbitSum();
}

echo $sum, "\n";
