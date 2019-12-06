<?php

$orbitStrings = explode("\n", rtrim(file_get_contents('d6.txt')));

class Orbit
{
	private $orbitName;

	private $parentOrbit;

	private $orbitSum;

	function __construct(string $orbitName, ?string $parentOrbit)
	{
		$this->orbitName = $orbitName;
		$this->parentOrbit = $parentOrbit;
	}

	public function getParent() : ?string
	{
		return $this->parentOrbit;
	}

	public function __debugInfo()
	{
		return [
			'orbitName' => $this->orbitName,
			'parentOrbit' => $this->parentOrbit,
		];
	}
}

$orbits = ['COM' => new Orbit('COM', null)];

foreach ($orbitStrings as $orbit)
{
	preg_match('%(.+)\)(.*)%', $orbit, $matches);
	[, $orbitee, $orbiter] = $matches;

	$orbits[$orbiter] = new Orbit($orbiter, $orbitee);
}

$youOrbit = $orbits['YOU']->getParent();
$youHops = 0;
$youOrbitsSeen = [$youOrbit => $youHops];
$sanOrbit = $orbits['SAN']->getParent();
$sanHops = 0;
$sanOrbitsSeen = [$sanOrbit => $sanHops];

// echo "You: ", join(', ', array_keys($youOrbitsSeen)), "\n";
// echo "San: ", join(', ', array_keys($sanOrbitsSeen)), "\n";
// echo "\n";

$intersect = [];
while(! count($intersect))
{
	if ($youOrbit !== 'COM')
	{
		$youOrbit = $orbits[$youOrbit]->getParent();
		$youHops++;
		$youOrbitsSeen[$youOrbit] = $youHops;
	}

	if ($sanOrbit !== 'COM')
	{
		$sanOrbit = $orbits[$sanOrbit]->getParent();
		$sanHops++;
		$sanOrbitsSeen[$sanOrbit] = $sanHops;
	}

	$intersect = array_intersect_key($youOrbitsSeen, $sanOrbitsSeen);

	// echo "You: ", join(', ', array_keys($youOrbitsSeen)), "\n";
	// echo "San: ", join(', ', array_keys($sanOrbitsSeen)), "\n";
	// echo "Intersect: ", join(', ', array_keys($intersect)), "\n";
	// echo "\n";
	// usleep(100);
}

$key = key($intersect);

echo $youOrbitsSeen[$key] + $sanOrbitsSeen[$key], "\n";
