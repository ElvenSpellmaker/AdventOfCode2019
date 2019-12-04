<?php

$wires = explode("\n", rtrim(file_get_contents('d3.txt')));

$positions = [];

$x = 0;
$y = 0;

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
				$positions[$y][$x] = true;
			}
		break;
		case 'L':
			for ($i = 0; $i < $amount; $i++)
			{
				$x--;
				$positions[$y][$x] = true;
			}
		break;
		case 'D':
			for ($i = 0; $i < $amount; $i++)
			{
				$y++;
				$positions[$y][$x] = true;
			}
		break;
		case 'R':
			for ($i = 0; $i < $amount; $i++)
			{
				$x++;
				$positions[$y][$x] = true;
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

foreach ($matches as [, $direction, $amount])
{
	switch ($direction)
	{
		case 'U':
			for ($i = 0; $i < $amount; $i++)
			{
				$y--;

				if (isset($positions[$y][$x]) && (abs($x) + abs($y)) < abs($closestCrossover[0]) + abs($closestCrossover[1]))
				{
					$closestCrossover[0] = $x;
					$closestCrossover[1] = $y;
					$smallestSum = abs($x) + abs($y);
				}
			}
		break;
		case 'L':
			for ($i = 0; $i < $amount; $i++)
			{
				$x--;

				if (isset($positions[$y][$x]) && (abs($x) + abs($y)) < abs($closestCrossover[0]) + abs($closestCrossover[1]))
				{
					$closestCrossover[0] = $x;
					$closestCrossover[1] = $y;
					$smallestSum = abs($x) + abs($y);
				}
			}
		break;
		case 'D':
			for ($i = 0; $i < $amount; $i++)
			{
				$y++;

				if (isset($positions[$y][$x]) && (abs($x) + abs($y)) < abs($closestCrossover[0]) + abs($closestCrossover[1]))
				{
					$closestCrossover[0] = $x;
					$closestCrossover[1] = $y;
					$smallestSum = abs($x) + abs($y);
				}
			}
		break;
		case 'R':
			for ($i = 0; $i < $amount; $i++)
			{
				$x++;

				if (isset($positions[$y][$x]) && (abs($x) + abs($y)) < abs($closestCrossover[0]) + abs($closestCrossover[1]))
				{
					$closestCrossover[0] = $x;
					$closestCrossover[1] = $y;
					$smallestSum = abs($x) + abs($y);
				}
			}
		break;
	}
}

echo $closestCrossover[0], ',', $closestCrossover[1], ' --> ', $smallestSum, "\n";
