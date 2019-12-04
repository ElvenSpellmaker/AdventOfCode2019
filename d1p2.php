<?php

$modules = explode("\n", file_get_contents('d1.txt'));
array_pop($modules);

function fuel_summer($initial, $module)
{
	$mass = floor($module / 3) - 2;

	if ($mass <= 0)
	{
		return $initial;
	}

	return fuel_summer($initial + $mass, $mass);
}

$sum = array_reduce(
	$modules,
	function($carry, $item) { return $carry + fuel_summer(0, $item); },
	0,
);

echo $sum;
