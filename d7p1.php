<?php

$commands = explode(',', rtrim(file_get_contents(__DIR__ . '/d7.txt')));

// $commands = ['3', '15', '3', '16', '1002', '16', '10', '16', '1', '16', '15', '15', '4', '15', '99', '0', '0'];
// $commands = ['3', '23', '3', '24', '1002', '24', '10', '24', '1002', '23', '-1', '23', '101', '5', '23', '23', '1', '24', '23', '23', '4', '23', '99', '0', '0'];
// $commands = ['3', '31', '3', '32', '1002', '32', '10', '32', '1001', '31', '-2', '31', '1007', '31', '0', '33', '1002', '33', '7', '33', '1', '33', '31', '31', '1', '32', '31', '31', '4', '31', '99', '0', '0', '0'];

$phaseList = [0, 1, 2, 3, 4];
$origCommands = $commands;

// Adapted from Stackoverflow: https://stackoverflow.com/a/27160465/2604915
function permutations(array $elements)
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
					array_slice($permutation, $i)
				);
			}
		}
	}
}

const POSITION_MODE = '0';
const IMMEDIATE_MODE = '1';

function parse_opcode($opcode): array
{
	$opcode = str_split($opcode);

	$opcodeArray = [];

	switch (count($opcode))
	{
		case 5:
			$opcodeArray = [$opcode[0], $opcode[1], $opcode[2], $opcode[3] . $opcode[4]];
		break;
		case 4:
			$opcodeArray = ['0', $opcode[0], $opcode[1], $opcode[2] . $opcode[3]];
		break;
		case 3:
			$opcodeArray = ['0', '0', $opcode[0], $opcode[1] . $opcode[2]];
		break;
		case 2:
			$opcodeArray = ['0', '0', '0', $opcode[0] . $opcode[1]];
		break;
		case 1:
			$opcodeArray = ['0', '0', '0', '0' . $opcode[0]];
		break;
		default:
			throw new Exception('Unknown size for Opcode!');
		break;
	}

	return $opcodeArray;
}

$bestThrusterVal = -INF;
foreach(permutations($phaseList) as $permutation)
{
	$commands = $origCommands;

	// $permutation = [4,3,2,1,0];
	$inputInstructions = [0];

	// echo 'Permuations: ', join($permutation), "\n";
	for ($i = 0; $i < 5; $i++)
	{
		$inputInstructions[] = array_shift($permutation);
		// echo "{$inputInstructions[0]}, {$inputInstructions[1]}\n";
		$pos = 0;
		while (true)
		{
			[$mode3, $mode2, $mode1, $opcode] = parse_opcode($commands[$pos]);

			// echo "Pass: ", ++$i, "\n";
			// echo "Opcode: ", $opcode, "\n";

			// var_dump($mode3, $mode2, $mode1, $opcode, $pos);

			switch ($opcode)
			{
				case '01':
					$val1 = $mode1 === POSITION_MODE
						? $commands[$commands[$pos + 1]]
						: $commands[$pos + 1];
					$val2 = $mode2 === POSITION_MODE
						? $commands[$commands[$pos + 2]]
						: $commands[$pos + 2];

					$placePos = $commands[$pos + 3];

					$commands[$placePos] = (string) ($val1 + $val2);

					$pos += 4;
				break;
				case '02':
					$val1 = $mode1 === POSITION_MODE
						? $commands[$commands[$pos + 1]]
						: $commands[$pos + 1];
					$val2 = $mode2 === POSITION_MODE
						? $commands[$commands[$pos + 2]]
						: $commands[$pos + 2];

					$placePos = $commands[$pos + 3];

					$commands[$placePos] = (string) ($val1 * $val2);

					$pos += 4;
				break;
				case '03':
					$commands[$commands[$pos + 1]] = (string) array_pop($inputInstructions);
					$pos += 2;
				break;
				case '04':
					$output = $mode1 === POSITION_MODE
						? $commands[$commands[$pos + 1]]
						: $commands[$pos + 1];

					// echo $output, "\n";
					$inputInstructions = [$output];

					$pos += 2;
				break;
				case '05':
					$val1 = $mode1 === POSITION_MODE
						? $commands[$commands[$pos + 1]]
						: $commands[$pos + 1];
					$val2 = $mode2 === POSITION_MODE
						? $commands[$commands[$pos + 2]]
						: $commands[$pos + 2];

					if ($val1)
					{
						$pos = $val2;
						continue 2;
					}

					$pos += 3;
				break;
				case '06':
					$val1 = $mode1 === POSITION_MODE
						? $commands[$commands[$pos + 1]]
						: $commands[$pos + 1];
					$val2 = $mode2 === POSITION_MODE
						? $commands[$commands[$pos + 2]]
						: $commands[$pos + 2];

					if (!$val1)
					{
						$pos = $val2;
						continue 2;
					}

					$pos += 3;
				break;
				case '07':
					$val1 = $mode1 === POSITION_MODE
						? $commands[$commands[$pos + 1]]
						: $commands[$pos + 1];
					$val2 = $mode2 === POSITION_MODE
						? $commands[$commands[$pos + 2]]
						: $commands[$pos + 2];

					// echo "Is '$val1' < '$val2'?\n";

					$commands[$commands[$pos + 3]] = (int) ($val1 < $val2);

					$pos += 4;
				break;
				case '08':
					$val1 = $mode1 === POSITION_MODE
						? $commands[$commands[$pos + 1]]
						: $commands[$pos + 1];
					$val2 = $mode2 === POSITION_MODE
						? $commands[$commands[$pos + 2]]
						: $commands[$pos + 2];

					// echo "Is '$val1' === '$val2'?\n";

					$commands[$commands[$pos + 3]] = (int) ($val1 === $val2);

					$pos += 4;
				break;
				default:
					// Unknown Opcode, break out!
				break 2;
			}
		}
	}

	if ($inputInstructions[0] > $bestThrusterVal)
	{
		$bestThrusterVal = $inputInstructions[0];
	}
}

echo $bestThrusterVal, "\n";
