<?php

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

$runs = 1000;

while ($runs--)
{
	$totalEnergy = 0;
	foreach ($moons as $moonPos => &$moon)
	{
		applyGravity($moon, $moonPos, $moons);
	}

	foreach ($moons as &$moon)
	{
		moveMoon($moon);
		$totalEnergy += calculateMoonEnergy($moon);
	}

}

echo $totalEnergy, "\n";

function applyGravity(array &$moon, int $moonPos, array $moons)
{
	$increase = [
		'x' => 0,
		'y' => 0,
		'z' => 0,
	];

	foreach ($moons as $currentPos => $currentMoon)
	{
		if ($moonPos === $currentPos)
		{
			continue;
		}

		foreach (COÖRDINATES as $coördinate)
		{
			$increase[$coördinate] += $currentMoon[$coördinate] <=> $moon[$coördinate];
		}
	}

	foreach (COÖRDINATES as $coördinate)
	{
		$moon['d' . $coördinate] += $increase[$coördinate];
	}
}

function moveMoon(array &$moon)
{
	foreach (COÖRDINATES as $coördinate)
	{
		$moon[$coördinate] += $moon['d' . $coördinate];
	}
}

function calculateMoonEnergy(array $moon)
{
	return (abs($moon['x']) + abs($moon['y']) + abs($moon['z']))
		* (abs($moon['dx']) + abs($moon['dy']) + abs($moon['dz']));
}
