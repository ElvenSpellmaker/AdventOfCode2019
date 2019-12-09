<?php

$pixels = rtrim(file_get_contents('d8.txt'));

$wide = 25;
$tall = 6;

$size = $wide * $tall;

$layers = str_split($pixels, $size);
$layer = array_pop($layers);
$layers = array_reverse($layers);

$image = [];

for ($y = 0; $y < $tall; $y++)
{
	$ySize = $y * $wide;

	for ($x = 0; $x < $wide; $x++)
	{
		$image[$y][$x] = $layer[$x + $ySize];
	}
}

foreach ($layers as $layer)
{
	for ($y = 0; $y < $tall; $y++)
	{
		$ySize = $y * $wide;

		for ($x = 0; $x < $wide; $x++)
		{
			if ($layer[$x + $ySize] !== '2')
			{
				$image[$y][$x] = $layer[$x + $ySize];
			}
		}
	}
}

$black = "\e[40m ";
$white = "\e[107m ";
$reset = "\e[49m ";

foreach ($image as $y => $row)
{
	foreach ($row as $x)
	{
		switch ($x)
		{
			case '0':
				echo $black;
			break;
			case '1':
				echo $white;
			break;
			case '2':
				echo $reset;
			break;
		}
	}

	echo "\e[0m\n";
}
