<?php

$modules = explode("\n", file_get_contents('d1.txt'));
array_pop($modules);


$sum = array_reduce(
	$modules,
	function($carry, $module) {
		$mass = floor($module / 3) - 2;

		return $carry + $mass;
	},
	0,
);

echo $sum;
