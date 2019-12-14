<?php

use Elvenspellmaker\AdventOfCode2019\Intcode;
use ElvenSpellmaker\PipeSys\IO\ReadIntent;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/d9intcode.php';

const EMPTY_TILE = '0';
const WALL_TILE = '1';
const BLOCK_TILE = '2';
const HORIZONTAL_PADDLE_TILE = '3';
const BALL_TILE = '4';

const JOYSTICK_NEUTRAL = '0';
const JOYSTICK_LEFT = '-1';
const JOYSTICK_RIGHT = '1';

$commands = explode(',', rtrim(file_get_contents('d13.txt')));
$commands[0] = 2;

$joystick = JOYSTICK_NEUTRAL;

$tiles = [];

$inputInstructions = [];

$ballX = 0;
$paddleX = 0;

$intcode = new Intcode($commands, $inputInstructions);

$gen = $intcode->getCommand();

$score = 0;

foreach ($gen as $output)
{
	if ($output instanceof ReadIntent)
	{
		$diff = $ballX <=> $paddleX;
		$output = $gen->send($diff);

		// $ySize = 0;
		// $xSize = 0;
		// foreach ($tiles as $row)
		// {
		// 	$ySize++;
		// 	$x = 0;
		// 	foreach ($row as $tile)
		// 	{
		// 		$x++;
		// 	}

		// 	if ($x > $xSize)
		// 	{
		// 		$xSize = $x;
		// 	}
		// }

		// for ($y = 0; $y < $ySize; $y++)
		// {
		// 	for ($x = 0; $x < $xSize; $x++)
		// 	{
		// 		switch ($tiles[$y][$x] ?? ' ')
		// 		{
		// 			case EMPTY_TILE:
		// 			case ' ':
		// 				echo ' ';
		// 			break;
		// 			case WALL_TILE:
		// 				echo '#';
		// 			break;
		// 			case BLOCK_TILE:
		// 				echo '■';
		// 			break;
		// 			case HORIZONTAL_PADDLE_TILE:
		// 				echo '-';
		// 			break;
		// 			case BALL_TILE:
		// 				echo 'O';
		// 			break;
		// 		}
		// 		// echo $tiles[$y][$x] ?? ' ';
		// 	}
		// 	echo "\n";
		// }

		// echo "Score: ", $score, "\n";

		// exit;
	}

	$x = $output->getContent();

	$gen->next();
	$output = $gen->current();

	$y = $output->getContent();

	$gen->next();
	$output = $gen->current();

	if ($x === '-1' && $y === '0')
	{
		$score = $output->getContent();
	}
	else
	{
		$tile = $output->getContent();
		if ($tile === BALL_TILE)
		{
			$ballX = $x;
		}

		if ($tile === HORIZONTAL_PADDLE_TILE)
		{
			$paddleX = $x;
		}

		$tiles[$y][$x] = $tile;
	}
}

echo "Score: ", $score, "\n";
