<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/d7intcode.php';

use ElvenSpellmaker\PipeSys as PS;
use ElvenSpellmaker\PipeSys\Command as Command;
use ElvenSpellmaker\PipeSys\IO\QueueBuffer;
use Elvenspellmaker\AdventOfCode2019\Amplifier;

/** @var array $commands */
$commands = explode(',', rtrim(file_get_contents(__DIR__ . '/d7.txt')));

// $commands = ['3', '26', '1001', '26', '-4', '26', '3', '27', '1002', '27', '2', '27', '1', '27', '26', '27', '4', '27', '1001', '28', '-1', '28', '1005', '28', '6', '99', '0', '0', '5'];
// $commands = ['3', '52', '1001', '52', '-5', '52', '3', '53', '1', '52', '56', '54', '1007', '54', '5', '55', '1005', '55', '26', '1001', '54',  '-5', '54', '1105', '1', '12', '1', '53', '54', '53', '1008', '54', '0', '55', '1001', '55', '1', '55', '2', '53', '55', '53', '4',  '53', '1001', '56', '-1', '56', '1005', '56', '6', '99', '0', '0', '0', '0', '10'];

$phaseList = [5, 6, 7, 8, 9];

// Adapted from Stackoverflow: https://stackoverflow.com/a/27160465/2604915
function permutations(array $elements) : Generator
{
	if (count($elements) <= 1)
	{
		yield $elements;
	}
	else
	{
		foreach (permutations(array_slice($elements, 1)) as $permutation)
		{
			foreach (range(0, count($elements) - 1) as $i)
			{
				yield array_merge(
					array_slice($permutation, 0, $i),
					[$elements[0]],
					array_slice($permutation, $i),
				);
			}
		}
	}
}

$bestThrusterVal = -INF;
$connector = new Command\StandardConnector;
foreach(permutations($phaseList) as $permutation)
{
	// echo join($permutation), "\n";

	/** @var Amplifier $amplifiers */
	$amplifiers = [];
	$c = new PS\Scheduler($connector);
	// echo 'Permuations: ', join($permutation), "\n";
	for ($i = 0; $i < 5; $i++)
	{
		$inputArray = ($i === 0) ? [0, $permutation[$i]] : [$permutation[$i]];

		$amp = new Amplifier($commands, $inputArray, $i);
		$amplifiers[] = $amp;
		$c->addCommand($amp);
	}

	// Set up a special loop buffer for outputting the End Amp into the Beginning Amp
	$buffer = new QueueBuffer;
	$amplifiers[0]->setStdIn($buffer);
	$amplifiers[4]->setStdOut($buffer);

	$c->run();

	$output = $amplifiers[4]->getOutput();

	if ($output > $bestThrusterVal)
	{
		$bestThrusterVal = $output;
	}
}

echo $bestThrusterVal, "\n";
