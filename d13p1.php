<?php

use Elvenspellmaker\AdventOfCode2019\Intcode;
use ElvenSpellmaker\PipeSys\IO\OutputIntent;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/d9intcode.php';

const EMPTY_TILE = '0';
const WALL_TILE = '1';
const BLOCK_TILE = '2';
const HORIZONTAL_PADDLE_TILE = '3';
const BALL_TILE = '4';

$commands = explode(',', rtrim(file_get_contents('d13.txt')));

$tiles = [];
$numberTwoTiles = 0;

$inputInstructions = [];

$intcode = new Intcode($commands, $inputInstructions);

$gen = $intcode->getCommand();

/** @var OutputIntent $output */
foreach ($gen as $output)
{
	$x = $output->getContent();

	$gen->next();
	$output = $gen->current();

	$y = $output->getContent();

	$gen->next();
	$output = $gen->current();

	$tiles[$y][$x] = $output->getContent();
}

foreach ($tiles as $row)
{
	foreach ($row as $c)
	{
		if ($c === BLOCK_TILE)
		{
			$numberTwoTiles++;
		}
	}
}

echo $numberTwoTiles, "\n";
