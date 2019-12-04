<?php

$commands = explode(',', rtrim(file_get_contents('d2.txt')));

$commands[1] = 12;
$commands[2] = 2;

$pos = 0;
while (in_array($commands[$pos], [1, 2, '1', '2']))
{
	$opcode = $commands[$pos];
	$pos1 = $commands[$pos + 1];
	$pos2 = $commands[$pos + 2];
	$placePos = $commands[$pos + 3];

	switch ($opcode)
	{
		case 1:
		case '1':
			$commands[$placePos] = $commands[$pos1] + $commands[$pos2];
		break;

		case 2:
		case '2':
			$commands[$placePos] = $commands[$pos1] * $commands[$pos2];
		break;
	}

	$pos += 4;
}

echo $commands[0];
