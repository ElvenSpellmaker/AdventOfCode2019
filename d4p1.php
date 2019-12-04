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
	$lastSeen = null;
	$iString = (string)$i;
	for ($j = 0; $j < $passLength; $j++)
	{
		if ($lastSeen > $iString[$j])
		{
			$decreaseSeen = true;
			break;
		}

		if ($lastSeen !== null && $lastSeen === $iString[$j])
		{
			$doubleSeen = true;
		}

		$lastSeen = $iString[$j];
	}

	if (! $doubleSeen || $decreaseSeen === true)
	{
		continue;
	}

	$validPasswords[] = $i;
}

echo count($validPasswords), "\n";
