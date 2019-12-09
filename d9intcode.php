<?php

namespace Elvenspellmaker\AdventOfCode2019;

use Exception;
use Generator;
use RuntimeException;
use ElvenSpellmaker\PipeSys\Command\AbstractCommand;
use ElvenSpellmaker\PipeSys\IO\OutputIntent;
use ElvenSpellmaker\PipeSys\IO\ReadIntent;

class Intcode extends AbstractCommand
{
	const POSITION_MODE = '0';
	const IMMEDIATE_MODE = '1';
	const RELATIVE_MODE = '2';

	private $commands;

	private $inputInstructions;

	private $output;

	private $relativeBase = 0;

	public function __construct(array $commands, array $inputInstructions)
	{
		$this->commands = $commands;
		$this->inputInstructions = $inputInstructions;
	}

	public function getOutput() : ?string
	{
		return $this->output;
	}

	public function getCommand() : Generator
	{
		$commands = $this->commands;
		$pos = 0;
		// $i = 0;

		while (true)
		{
			[$mode3, $mode2, $mode1, $opcode] = $this->parse_opcode($commands[$pos]);

			// echo "Pass: ", ++$i, "\n";
			// echo "Opcode: ", $opcode, "\n";

			// var_dump($mode3, $mode2, $mode1, $opcode, $pos);

			switch ($opcode)
			{
				case '01':
					$val1 = $this->getValue($commands, $pos + 1, $this->relativeBase, $mode1);
					$val2 = $this->getValue($commands, $pos + 2, $this->relativeBase, $mode2);

					$placePos = $mode3 === self::POSITION_MODE
						? $commands[$pos + 3]
						: $this->relativeBase + $commands[$pos + 3];

					$commands[$placePos] = (string) ($val1 + $val2);

					$pos += 4;
				break;
				case '02':
					$val1 = $this->getValue($commands, $pos + 1, $this->relativeBase, $mode1);
					$val2 = $this->getValue($commands, $pos + 2, $this->relativeBase, $mode2);

					$placePos = $mode3 === self::POSITION_MODE
						? $commands[$pos + 3]
						: $this->relativeBase + $commands[$pos + 3];

					$commands[$placePos] = (string) ($val1 * $val2);

					$pos += 4;
				break;
				case '03':
					$input = array_pop($this->inputInstructions) ?? yield new ReadIntent;

					$placePos = $mode1 === self::POSITION_MODE
						? $commands[$pos + 1]
						: $this->relativeBase + $commands[$pos + 1];

					$commands[$placePos] = (string) $input;
					$pos += 2;
				break;
				case '04':
					$output = $this->getValue($commands, $pos + 1, $this->relativeBase, $mode1);

					// echo $output, "\n";
					$this->output = $output;
					yield new OutputIntent($output);

					$pos += 2;
				break;
				case '05':
					$val1 = $this->getValue($commands, $pos + 1, $this->relativeBase, $mode1);
					$val2 = $this->getValue($commands, $pos + 2, $this->relativeBase, $mode2);

					if ($val1)
					{
						$pos = $val2;
						continue 2;
					}

					$pos += 3;
				break;
				case '06':
					$val1 = $this->getValue($commands, $pos + 1, $this->relativeBase, $mode1);
					$val2 = $this->getValue($commands, $pos + 2, $this->relativeBase, $mode2);

					if (!$val1)
					{
						$pos = $val2;
						continue 2;
					}

					$pos += 3;
				break;
				case '07':
					$val1 = $this->getValue($commands, $pos + 1, $this->relativeBase, $mode1);
					$val2 = $this->getValue($commands, $pos + 2, $this->relativeBase, $mode2);

					// echo "Is '$val1' < '$val2'?\n";

					$placePos = $mode3 === self::POSITION_MODE
						? $commands[$pos + 3]
						: $this->relativeBase + $commands[$pos + 3];

					$commands[$placePos] = (int) ($val1 < $val2);

					$pos += 4;
				break;
				case '08':
					$val1 = $this->getValue($commands, $pos + 1, $this->relativeBase, $mode1);
					$val2 = $this->getValue($commands, $pos + 2, $this->relativeBase, $mode2);

					// echo "Is '$val1' === '$val2'?\n";

					$placePos = $mode3 === self::POSITION_MODE
						? $commands[$pos + 3]
						: $this->relativeBase + $commands[$pos + 3];

					$commands[$placePos] = (int) ($val1 === $val2);

					$pos += 4;
				break;
				case '09':
					$value = $this->getValue($commands, $pos + 1, $this->relativeBase, $mode1);

					$this->relativeBase += $value;

					$pos += 2;
				break;
				case '99':
					// Halt Opcode.
				break 2;
				default:
					throw new RuntimeException('Unexpected Opcode!');
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

	private function getValue(array $commands, string $pos, string $relativeBase, string $mode) : ?string
	{
		$value = null;
		switch ($mode)
		{
			case self::POSITION_MODE:
				$value = $commands[$commands[$pos]] ?? 0;
			break;
			case self::IMMEDIATE_MODE:
				$value = $commands[$pos] ?? 0;
			break;
			case self::RELATIVE_MODE:
				$value = $commands[$relativeBase + $commands[$pos]] ?? 0;
			break;
			default:
				throw new RuntimeException('Unknown mode!');
		}

		return $value;
	}
}
