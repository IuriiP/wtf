<?php

/*
 * Datasets aliased list with specific options and parameters.
 * 
 */

return [
	'default' => [
		'engine' => 'mysql',
		'host' => 'localhost',
		'name' => 'my_db',
		'user' => 'root',
		'password' => 'password',
		'persistent' => true,
		'observe' => [
			'' => 'Logger',
		]
	],
	'log' => [
		'engine' => 'vertica',
		'host' => 'vertica.com',
		'name' => 'my_log_db',
		'user' => 'user',
		'password' => 'password',
	],
	'play' => [
		'engine' => 'redis',
		'host' => 'localhost',
		'name' => 'play_db',
		'user' => 'user',
		'password' => 'password',
	],
	'cloud' => [
		'engine' => 'amazon',
		'host' => 'amazon.com',
		'name' => 'my_cloud_db',
		'user' => 'user',
		'password' => 'password',
	],
];
