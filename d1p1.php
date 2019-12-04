<?php

$modules = explode("\n", rtrim(file_get_contents(__DIR__ . '/d1.txt')));

$sum = array_reduce(
	$modules,
	function ($carry, $module) {
		$mass = floor($module / 3) - 2;

		return $carry + $mass;
	},
	0,
);

echo $sum, "\n";
