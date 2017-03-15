<?php

/*
 * .php config
 */
return [
	'string' => 'Overrided string',
	'object' => new \stdClass(),
	'array' => [
		'bool' => true,
		'numeric' => 999,
	],
	'indirect' => $_SERVER['SCRIPT_FILENAME'],
];
