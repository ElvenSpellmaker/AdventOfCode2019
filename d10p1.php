<?php

$rows = explode("\n", rtrim(file_get_contents('d10.txt')));

// $rows = ['.#..#',
// '.....',
// '#####',
// '....#',
// '...##'];

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

$top = ['count' => 0];
$countAsteroids = [];
foreach ($asteroids as $y => $row)
{
	foreach ($row as $x => $cell)
	{
		computeVisibleCount($x, $y, $asteroids, $countAsteroids, $top);
	}
}

// var_dump($countAsteroids);

echo $top['x'], ',', $top['y'], ' --> ', $top['count'], "\n";

function computeVisibleCount(int $x, int $y, array $asteroids, array &$countAsteroids, array &$top) : void
{
	// Ensure we're not detecting ourself.
	unset($asteroids[$y][$x]);

	$count = 0;

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

			$manhattanDistance = abs($cx + $x) + abs($cy + $y);

			if (! isset($counts[$gradient]))
			{
				$counts[$gradient] = [
					[
						'dist' => $manhattanDistance,
						'x' => $cx,
						'y' => $cy,
						'dx' => $xMinus,
						'dy' => $yMinus,
					],
				];

				$count++;
			}

			if (count($counts[$gradient]) === 1 && checkValidSameGradient($xMinus, $yMinus, $counts[$gradient]))
			{
				$counts[$gradient][] = [
					'dist' => $manhattanDistance,
					'x' => $cx,
					'y' => $cy,
					'dx' => $xMinus,
					'dy' => $yMinus,
				];

				$count++;
			}
		}
	}

	$countAsteroids[$y][$x] = count($counts);

	if ($count > $top['count'])
	{
		$top = [
			'x' => $x,
			'y' => $y,
			'count' => $count,
		];
	}
}

function checkValidSameGradient($dx, $dy, array $sameGradientPoint) : bool
{
	[0 => ['dx' => $dx2, 'dy' => $dy2]] = $sameGradientPoint;

	if ($dx < 0 && $dx2 > 0)
	{
		return true;
	}

	if ($dx > 0 && $dx2 < 0)
	{
		return true;
	}

	if ($dy < 0 && $dy2 > 0)
	{
		return true;
	}

	if ($dy > 0 && $dy2 < 0)
	{
		return true;
	}

	return false;
}
