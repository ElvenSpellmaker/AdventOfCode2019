<?php

$wires = explode("\n", rtrim(file_get_contents('d3.txt')));

$positions = [];

$x = 0;
$y = 0;
$steps = 1;

$firstWire = $wires[0];
preg_match_all('%([ULDR])(\d+)%', $firstWire, $matches, PREG_SET_ORDER);

foreach ($matches as [, $direction, $amount])
{
	switch ($direction)
	{
		case 'U':
			for ($i = 0; $i < $amount; $i++)
			{
				$y--;
				$positions[$y][$x] = $steps++;
			}
		break;
		case 'L':
			for ($i = 0; $i < $amount; $i++)
			{
				$x--;
				$positions[$y][$x] = $steps++;
			}
		break;
		case 'D':
			for ($i = 0; $i < $amount; $i++)
			{
				$y++;
				$positions[$y][$x] = $steps++;
			}
		break;
		case 'R':
			for ($i = 0; $i < $amount; $i++)
			{
				$x++;
				$positions[$y][$x] = $steps++;
			}
		break;
	}
}

$secondWire = $wires[1];
preg_match_all('%([ULDR])(\d+)%', $secondWire, $matches, PREG_SET_ORDER);

$closestCrossover = [INF, INF];
$smallestSum = INF;

$x = 0;
$y = 0;
$steps = 0;
$minSteps = INF;

foreach ($matches as [, $direction, $amount])
{
	switch ($direction)
	{
		case 'U':
			for ($i = 0; $i < $amount; $i++)
			{
				$y--;
				$steps++;

				if (isset($positions[$y][$x]))
				{
					$crossSteps = $positions[$y][$x] + $steps;
					if ($crossSteps < $minSteps)
					{
						$minSteps = $crossSteps;
					}
				}
			}
		break;
		case 'L':
			for ($i = 0; $i < $amount; $i++)
			{
				$x--;
				$steps++;

				if (isset($positions[$y][$x]))
				{
					$crossSteps = $positions[$y][$x] + $steps;
					if ($crossSteps < $minSteps)
					{
						$minSteps = $crossSteps;
					}
				}
			}
		break;
		case 'D':
			for ($i = 0; $i < $amount; $i++)
			{
				$y++;
				$steps++;

				if (isset($positions[$y][$x]))
				{
					$crossSteps = $positions[$y][$x] + $steps;
					if ($crossSteps < $minSteps)
					{
						$minSteps = $crossSteps;
					}
				}
			}
		break;
		case 'R':
			for ($i = 0; $i < $amount; $i++)
			{
				$x++;
				$steps++;

				if (isset($positions[$y][$x]))
				{
					$crossSteps = $positions[$y][$x] + $steps;
					if ($crossSteps < $minSteps)
					{
						$minSteps = $crossSteps;
					}
				}
			}
		break;
	}
}

echo $minSteps;
