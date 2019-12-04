<?php

$min = 125730;
$max = 579381;

$validPasswords = [];
for ($i = $min; $i <= $max; $i++)
{
	$passLength = strlen($i);

	if ($passLength !== 6)
	{
		continue;
	}

	if ($i < $min || $i > $max)
	{
		continue;
	}

	$decreaseSeen = false;
	$doubleSeen = false;
	$tripleSeen = false;
	$lastSeen = null;
	$iString = (string)$i;
	//$iString = (string)'578888';
	for ($j = 0; $j < $passLength; $j++)
	{
		if ($lastSeen > $iString[$j])
		{
			$decreaseSeen = true;
			break;
		}

		if ($lastSeen !== null && $lastSeen === $iString[$j])
		{
			if (isset($iString[$j + 1]) && $iString[$j] === $iString[$j + 1])
			{
				$tripleSeen = true;

				while (isset($iString[$j + 1]) && $lastSeen === $iString[$j + 1])
				{
					$j++;
				}
			}
			else
			{
				$doubleSeen = true;
			}

		}

		$lastSeen = $iString[$j];
	}

	// var_dump($doubleSeen, $tripleSeen, $decreaseSeen, ($tripleSeen && ! $doubleSeen), ($tripleSeen && ! $doubleSeen) || ! $doubleSeen || $decreaseSeen);exit;

	if (($tripleSeen && ! $doubleSeen) || ! $doubleSeen || $decreaseSeen)
	{
		continue;
	}

	$validPasswords[] = $i;
}

// var_dump($validPasswords);

echo count($validPasswords), "\n";
