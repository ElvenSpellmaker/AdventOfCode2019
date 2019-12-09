<?php

use Elvenspellmaker\AdventOfCode2019\Intcode;
use ElvenSpellmaker\PipeSys\IO\OutputIntent;
use ElvenSpellmaker\PipeSys\IO\ReadIntent;

$commands = explode(',', rtrim(file_get_contents('d9.txt')));

// $commands = ['109', '1', '204', '-1', '1001', '100', '1', '100', '1008', '100', '16', '101', '1006', '101', '0', '99'];
// $commands = ['1102', '34915192', '34915192', '7', '4', '7', '99', '0'];
// $commands = ['104', '1125899906842624', '99'];

require 'vendor/autoload.php';
require 'd9intcode.php';

$intcode = new Intcode($commands, [1]);

foreach ($intcode->getCommand() as $i)
{
	if ($i instanceof OutputIntent)
	{
		echo $i->getContent(), "\n";
	}

	if ($i instanceof ReadIntent)
	{
		throw new RuntimeException('Not expecting any reads!');
	}
}
