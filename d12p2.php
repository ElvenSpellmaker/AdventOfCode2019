<?php

ini_set('memory_limit', '2G');

$moonString = rtrim(file_get_contents('d12.txt'));

// $moonString = '<x=-1, y=0, z=2>
// <x=2, y=-10, z=-7>
// <x=4, y=-8, z=8>
// <x=3, y=5, z=-1>';

const COÖRDINATES = ['x', 'y', 'z'];

preg_match_all('%<x=(-?\d+), y=(-?\d+), z=(-?\d+)>%', $moonString, $matches, PREG_SET_ORDER);

$moons = [];
foreach ($matches as $match)
{
	$moons[] = [
		'x' => $match[1],
		'y' => $match[2],
		'z' => $match[3],
		'dx' => 0,
		'dy' => 0,
		'dz' => 0,
	];
}

$cycleLengths = [
	'x' => 0,
	'y' => 0,
	'z' => 0,
];

$firstValues = [
	'x' => array_column($moons, 'x'),
	'y' => array_column($moons, 'y'),
	'z' => array_column($moons, 'z'),
];

foreach (COÖRDINATES as $coördinate)
{
	$runs = true;
	$cycle = [];
	while ($runs--)
	{
		// foreach ($moons as $moon)
		// {
		// 	echo $moon[$coördinate], ',';
		// }
		// echo "\n";

		foreach ($moons as $moonPos => &$moon)
		{
			applyGravity($moon, $moonPos, $moons, $coördinate);
		}

		foreach ($moons as &$moon)
		{
			// echo 'move:';
			moveMoon($moon, $coördinate);
			// echo "\n";
		}

		unset($moon);

		$currentCycle = array_column($moons, $coördinate);
		$cycle[] = $currentCycle;

		// var_dump($firstValues[$coördinate], $currentCycle);

		$count = count($cycle);
		if ($count % 2 === 0 && $firstValues[$coördinate] == $currentCycle)
		{
			[$c1, $c2] = array_chunk($cycle, $count / 2);

			if ($c1 === $c2)
			{
				$cycleLengths[$coördinate] = $count / 2;
				continue 2;
			}
		}
	}
}

echo gmp_lcm($cycleLengths['x'], gmp_lcm($cycleLengths['y'], $cycleLengths['z'])), "\n";

function applyGravity(array &$moon, int $moonPos, array $moons, string $coördinate) : void
{
	$increase = 0;

	foreach ($moons as $currentPos => $currentMoon)
	{
		if ($moonPos === $currentPos)
		{
			continue;
		}

		$increase += $currentMoon[$coördinate] <=> $moon[$coördinate];
	}

	$moon['d' . $coördinate] += $increase;
}

function moveMoon(array &$moon, string $coördinate) : void
{
	// echo $moon[$coördinate], ":", $moon['d' . $coördinate];
	$moon[$coördinate] += $moon['d' . $coördinate];

	// echo "--> {$moon[$coördinate]}";
}
