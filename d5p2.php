<?php

$commands = explode(',', rtrim(file_get_contents(__DIR__ . '/d5.txt')));

// $commands = ['3', '0', '4', '0', '99'];
// $commands = ['3', '9', '8', '9', '10', '9', '4', '9', '99', '-1', '8'];
// $commands = ['3', '9', '7', '9', '10', '9', '4', '9', '99', '-1', '8'];
// $commands = ['3', '3', '1108', '-1', '8', '3', '4', '3', '99'];
// $commands = ['3', '3', '1107', '-1', '8', '3', '4', '3', '99'];

$inputInstructions = [5];

const POSITION_MODE = '0';
const IMMEDIATE_MODE = '1';

function parse_opcode($opcode) : array
{
	$opcode = str_split($opcode);

	$opcodeArray = [];

	switch  (count($opcode))
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

$i = 0;
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

			$commands[$placePos] = (string)($val1 + $val2);

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

			$commands[$placePos] = (string)($val1 * $val2);

			$pos += 4;
		break;
		case '03':
			$commands[$commands[$pos + 1]] = (string)array_pop($inputInstructions);
			$pos += 2;
		break;
		case '04':
			$output = $mode1 === POSITION_MODE
				? $commands[$commands[$pos + 1]]
				: $commands[$pos + 1];

			echo $output, "\n";

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

			if (! $val1)
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

			$commands[$commands[$pos + 3]] = (int)($val1 < $val2);

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

			$commands[$commands[$pos + 3]] = (int)($val1 === $val2);

			$pos += 4;
		break;
		default:
			// Unknown Opcode, break out!
		break 2;
	}
}
