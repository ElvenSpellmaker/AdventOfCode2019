<?php

namespace Elvenspellmaker\AdventOfCode2019;

use Exception;
use Generator;
use ElvenSpellmaker\PipeSys\Command\AbstractCommand;
use ElvenSpellmaker\PipeSys\IO\OutputIntent;
use ElvenSpellmaker\PipeSys\IO\ReadIntent;

class Amplifier extends AbstractCommand
{
	const POSITION_MODE = '0';
	const IMMEDIATE_MODE = '1';

	private $commands;

	private $inputInstructions;

	private $output;

	private $name;

	public function __construct(array $commands, array $inputInstructions, string $name)
	{
		$this->commands = $commands;
		$this->inputInstructions = $inputInstructions;
		$this->name = $name;
	}

	public function getOutput() : ?string
	{
		return $this->output;
	}

	public function getCommand() : Generator
	{
		$commands = $this->commands;
		$pos = 0;
		$i = 0;

		while (true)
		{
			[$mode3, $mode2, $mode1, $opcode] = $this->parse_opcode($commands[$pos]);

			// echo "Command: ", $this->name, " Pass: ", ++$i, "\n";
			// echo "Command: ", $this->name, " Opcode: ", $opcode, "\n";

			// var_dump($mode3, $mode2, $mode1, $opcode, $pos);

			switch ($opcode)
			{
				case '01':
					$val1 = $mode1 === self::POSITION_MODE
						? $commands[$commands[$pos + 1]]
						: $commands[$pos + 1];
					$val2 = $mode2 === self::POSITION_MODE
						? $commands[$commands[$pos + 2]]
						: $commands[$pos + 2];

					$placePos = $commands[$pos + 3];

					$commands[$placePos] = (string) ($val1 + $val2);

					$pos += 4;
				break;
				case '02':
					$val1 = $mode1 === self::POSITION_MODE
						? $commands[$commands[$pos + 1]]
						: $commands[$pos + 1];
					$val2 = $mode2 === self::POSITION_MODE
						? $commands[$commands[$pos + 2]]
						: $commands[$pos + 2];

					$placePos = $commands[$pos + 3];

					$commands[$placePos] = (string) ($val1 * $val2);

					$pos += 4;
				break;
				case '03':
					// echo "Reading: {$this->name}";
					$input = array_pop($this->inputInstructions) ?? yield new ReadIntent;
					$commands[$commands[$pos + 1]] = (string) $input;
					$pos += 2;
				break;
				case '04':
					$output = $mode1 === self::POSITION_MODE
						? $commands[$commands[$pos + 1]]
						: $commands[$pos + 1];

					// echo $output, "\n";
					$this->output = $output;
					yield new OutputIntent($output);

					$pos += 2;
				break;
				case '05':
					$val1 = $mode1 === self::POSITION_MODE
						? $commands[$commands[$pos + 1]]
						: $commands[$pos + 1];
					$val2 = $mode2 === self::POSITION_MODE
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
					$val1 = $mode1 === self::POSITION_MODE
						? $commands[$commands[$pos + 1]]
						: $commands[$pos + 1];
					$val2 = $mode2 === self::POSITION_MODE
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
					$val1 = $mode1 === self::POSITION_MODE
						? $commands[$commands[$pos + 1]]
						: $commands[$pos + 1];
					$val2 = $mode2 === self::POSITION_MODE
						? $commands[$commands[$pos + 2]]
						: $commands[$pos + 2];

					// echo "Is '$val1' < '$val2'?\n";

					$commands[$commands[$pos + 3]] = (int) ($val1 < $val2);

					$pos += 4;
				break;
				case '08':
					$val1 = $mode1 === self::POSITION_MODE
						? $commands[$commands[$pos + 1]]
						: $commands[$pos + 1];
					$val2 = $mode2 === self::POSITION_MODE
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

	private function parse_opcode(string $opcode) : array
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
}
