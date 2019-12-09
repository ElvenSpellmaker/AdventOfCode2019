<?php

$pixels = rtrim(file_get_contents('d8.txt'));

$wide = 25;
$tall = 6;

$size = $wide * $tall;

$leastZeroDigits = INF;
$sum = 0;

$layers = str_split($pixels, $size);

foreach ($layers as $layer)
{
	$zeroDigits = 0;
	$oneDigits = 0;
	$twoDigits = 0;

	for ($i = 0; $i < $size; $i++)
	{
		switch ($layer[$i])
		{
			case '0':
				$zeroDigits++;

				if ($leastZeroDigits !== INF && $zeroDigits > $leastZeroDigits)
				{
					continue 2;
				}
			break;
			case '1':
				$oneDigits++;
			break;
			case '2':
				$twoDigits++;
			break;
		}
	}

	if ($zeroDigits < $leastZeroDigits)
	{
		$sum = $oneDigits * $twoDigits;
		$leastZeroDigits = $zeroDigits;
	}
}

echo $sum, "\n";
