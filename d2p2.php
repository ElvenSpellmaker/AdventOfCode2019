<?php

$commands = explode(',', rtrim(file_get_contents('d2.txt')));

$origCommands = $commands;
$output = 0;
$i1 = 1;
$i2 = 1;

while(true)
{
	$commands[1] = $i1;
	$commands[2] = $i2;

	$pos = 0;
	while (in_array($commands[$pos], [1, 2, '1', '2']))
	{
		$opcode = $commands[$pos];
		$pos1 = $commands[$pos + 1];
		$pos2 = $commands[$pos + 2];

		// echo "$pos1, $pos2\n";

		$placePos = $commands[$pos + 3];

		// echo "$pos1,$pos2 --> $placePos\n";

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

	$output = $commands[0];

	if ($output === 19690720)
	{
		break;
	}

	$i1++;

	if ($i1 === 99)
	{
		$i1 = 0;
		$i2++;
	}

	//echo $i1, ':', $i2, "\n";

	if ($i2 === 99)
	{
		echo "Hrm 99,99 and no answer...\n";
		exit;
	}

	// echo $commands[0], "\n";
	$commands = $origCommands;
	$pos = 0;
}

echo 100 * $i1 + $i2, "\n";
