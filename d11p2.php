<?php

use Elvenspellmaker\AdventOfCode2019\Intcode;
use ElvenSpellmaker\PipeSys\IO\OutputIntent;
use ElvenSpellmaker\PipeSys\IO\ReadIntent;

$instructions = explode(',', rtrim(file_get_contents('d11.txt')));

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/d9intcode.php';

$intcode = new Intcode($instructions, [1]);

$gen = $intcode->getCommand();

const UP = 0;
const LEFT = 1;
const RIGHT = 2;
const DOWN = 3;

$direction = UP;

$x = 0;
$y = 0;

$maxX = -INF;
$minX = INF;
$maxY = -INF;
$minY = INF;

$paintedTiles = 0;

$panels = [[1]];

$outputIntent = true;

while ($gen->valid() === true)
{
	// echo "{$x},{$y}\n";
	$command = $gen->current();

	if ($command instanceof OutputIntent)
	{
		if ($outputIntent === true)
		{
			$panels[$y][$x] = $intcode->getOutput();

			// var_dump($panels[$y][$x]);
		}
		else
		{
			$turn = $intcode->getOutput();

			// Work out new direction.
			switch ($direction)
			{
				case UP:
					$direction = $turn === '0'
						? LEFT
						: RIGHT;
				break;
				case LEFT:
					$direction = $turn === '0'
						? DOWN
						: UP;
				break;
				case RIGHT:
					$direction = $turn === '0'
						? UP
						: DOWN;
				break;
				case DOWN:
					$direction = $turn === '0'
						? RIGHT
						: LEFT;
				break;
			}

			// Move one space in the new direction.
			switch ($direction)
			{
				case UP:
					$y--;

					if ($y < $minY)
					{
						$minY = $y;
					}
				break;
				case LEFT:
					$x--;

					if ($x < $minX)
					{
						$minX = $x;
					}
				break;
				case RIGHT:
					$x++;

					if ($x > $maxX)
					{
						$maxX = $x;
					}
				break;
				case DOWN:
					$y++;

					if ($y > $maxY)
					{
						$maxY = $y;
					}
				break;
			}
		}

		$outputIntent = ! $outputIntent;
	}

	if ($command instanceof ReadIntent)
	{
		$gen->send($panels[$y][$x] ?? 0);
	}
	else
	{
		$gen->next();
	}
}

$black = "\e[40m ";
$white = "\e[107m ";

for ($y = $minY; $y <= $maxY; $y++)
{
	for ($x = $minX; $x <= $maxX; $x++)
	{
		if (isset($panels[$y][$x]) && $panels[$y][$x] === '1')
		{
			echo $white;
			continue;
		}

		echo $black;
	}

	echo "\e[0m\n";
}
