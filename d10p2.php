<?php

$rows = explode("\n", rtrim(file_get_contents('d10.txt')));

$rowCount = count($rows);
$columnCount = strlen($rows[0]);

$asteroids = [];

foreach ($rows as $y => &$row)
{
	$row = str_split($row);

	foreach ($row as $x => $cell)
	{
		if ($cell === '#')
		{
			$asteroids[$y][$x] = $cell;
		}
	}
}

$top = ['x' => '19', 'y' => '14', 'count' => 0];
$countAsteroids = [];

computeVisibleCount($top['x'], $top['y'], $asteroids, $countAsteroids);

// var_dump($countAsteroids);

function computeVisibleCount(int $x, int $y, array $asteroids, &$countAsteroids) : void
{
	// Ensure we're not detecting ourself.
	unset($asteroids[$y][$x]);

	$counts = [];

	foreach ($asteroids as $cy => $row)
	{
		foreach ($row as $cx => $cell)
		{
			$xMinus = $cx - $x;
			$yMinus = $cy - $y;
			$gradient = $xMinus === 0
				? (string)INF
				: (string)($yMinus / $xMinus);

			// echo "$gradient\n";

			$manhattanDistance = abs($cx - $x) + abs($cy - $y);

			$countAsteroids[$gradient][] = [
				'dist' => $manhattanDistance,
				'x' => $cx,
				'y' => $cy,
				'dx' => $xMinus,
				'dy' => $yMinus,
			];
		}
	}
}

// 19,10

$sortedLargest = array_keys($countAsteroids);
usort($sortedLargest, function($a, $b) {
	if ($a === 'INF')
	{
		$a = INF;
	}

	if ($b === 'INF')
	{
		$b = INF;
	}

	return (float)$a < (float)$b;
});

$blast = 200;
while (true)
{
	$fireRadar = $sortedLargest;

	// VERTICAL //
	$closestPoint = findClosestPoint($countAsteroids, 'INF', $top, $blast, true);
	if ($blast === 0)
	{
		break;
	}
	//////////////

	// TOP RIGHT //
	$currentGradient = array_pop($fireRadar);
	do
	{
		$closestPoint = findClosestPoint($countAsteroids, $currentGradient, $top, $blast, true);

		if ($blast === 0)
		{
			break 2;
		}

		$currentGradient = array_pop($fireRadar);
	}
	while ($currentGradient < 0);
	///////////////

	// Handle a zero right gradient //
	if ($currentGradient === 0)
	{
		$closestPoint = ['point' => ['dist' => INF]];
		foreach ($countAsteroids[$currentGradient] as $pointInLineKey => $pointInLine)
		{
			$closeMatch = $pointInLine['x'] > $top['x'];
			if ($closeMatch && $pointInLine['dist'] < $closestPoint['point']['dist'])
			{
				$closestPoint = [
					'point' => $pointInLine,
					'key' => $pointInLineKey,
				];
			}
		}

		if (isset($closestPoint['key']))
		{
			unset($countAsteroids[$currentGradient][$closestPoint['key']]);
			if (--$blast === 0)
			{
				break;
			}
		}
	}
	//////////////////////////////////

	// BOTTOM RIGHT //
	$currentGradient = array_pop($fireRadar);
	do
	{
		$closestPoint = findClosestPoint($countAsteroids, $currentGradient, $top, $blast, false);

		if ($blast === 0)
		{
			break 2;
		}

		$currentGradient = array_pop($fireRadar);
	}
	while ($currentGradient > 0);

	//////////////////

	// VERT BOTTOM //
	$closestPoint = findClosestPoint($countAsteroids, 'INF', $top, $blast, false);
	if ($blast === 0)
	{
		break;
	}
	/////////////////

	// RESET ARRAY
	$fireRadar = $sortedLargest;

	// BOTTOM LEFT //
	$currentGradient = array_pop($fireRadar);
	do
	{
		$closestPoint = findClosestPoint($countAsteroids, $currentGradient, $top, $blast, false);

		if ($closestPoint !== false)
		{
			break 2;
		}

		$currentGradient = array_pop($fireRadar);
	}
	while ($currentGradient < 0);
	/////////////////

	// Handle a zero left gradient //
	if ($currentGradient === 0)
	{
		$closestPoint = ['point' => ['dist' => INF]];
		foreach ($countAsteroids[$currentGradient] as $pointInLineKey => $pointInLine)
		{
			$closeMatch = $pointInLine['x'] < $top['x'];
			if ($closeMatch && $pointInLine['dist'] < $closestPoint['point']['dist'])
			{
				$closestPoint = [
					'point' => $pointInLine,
					'key' => $pointInLineKey,
				];
			}
		}

		if (isset($closestPoint['key']))
		{
			unset($countAsteroids[$currentGradient][$closestPoint['key']]);
			if (--$blast === 0)
			{
				break;
			}
		}
	}
	/////////////////////////////////

	// TOP LEFT //
	$currentGradient = array_pop($fireRadar);
	do
	{
		$closestPoint = findClosestPoint($countAsteroids, $currentGradient, $top, $blast, true);

		if ($closestPoint !== false)
		{
			break 2;
		}

		$currentGradient = array_pop($fireRadar);
	}
	while ($currentGradient > 0);
	//////////////
}

function findClosestPoint(array &$countAsteroids, string $currentGradient, array $top, int &$blast, bool $lessThan)
{
	$closestPoint = ['point' => ['dist' => INF]];
	foreach ($countAsteroids[$currentGradient] as $pointInLineKey => $pointInLine)
	{
		$closeMatch = $lessThan === true
			? $pointInLine['y'] < $top['y']
			: $pointInLine['y'] > $top['y'];

		if ($closeMatch && $pointInLine['dist'] < $closestPoint['point']['dist'])
		{
			$closestPoint = [
				'point' => $pointInLine,
				'key' => $pointInLineKey,
			];
		}
	}

	if (isset($closestPoint['key']))
	{
		//var_dump($countAsteroids[$currentGradient][$closestPoint['key']]);
		unset($countAsteroids[$currentGradient][$closestPoint['key']]);
		if (--$blast === 0)
		{
			return $closestPoint;
		}
	}

	return false;
}

function drawAndDump(array $asteroids, array $countAsteroids, array $top, int $blast, array $closestPoint)
{
	$n = [];
	foreach ($countAsteroids as $byGradients)
	{
		foreach ($byGradients as ['x' => $x, 'y' => $y])
		{
			$n[$y][$x] = true;
		}
	}

	for ($y = 0; $y < 25; $y++)
	{
		for ($x = 0; $x < 25; $x++)
		{
			if ($y == $top['y'] && $x == $top['x'])
			{
				echo 'X';
				continue;
			}

			if ($y == $closestPoint['point']['y'] && $x == $closestPoint['point']['x'])
			{
				echo 'V';
				continue;
			}

			if (! isset($n[$y][$x]))
			{
				if (isset($asteroids[$y][$x]))
				{
					echo 'B';
					continue;
				}

				echo '.';
				continue;
			}

			echo '#';
		}

		echo "\n";
	}

	var_dump($blast);exit;
}

// drawAndDump($asteroids, $countAsteroids, $top, $blast, $closestPoint);

echo $closestPoint['point']['x'], ',', $closestPoint['point']['y'], ' --> ', ($closestPoint['point']['x'] * 100) + $closestPoint['point']['y'], "\n";

// var_dump($closestPoint, $blast);
