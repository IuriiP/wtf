<?php

return [
	'dataset' => 'default',
	'domain' => 'user',
	'model' => [
		'id' => \Wtf\Dataset\Data::_(\Wtf\Dataset\Data::PRIMARY),
		'login' => \Wtf\Dataset\Data::_([\Wtf\Dataset\Data::STRING, 'UNIQUE']),
		'email' => \Wtf\Dataset\Data::_([\Wtf\Dataset\Data::STRING, 'UNIQUE']),
		'valid' => \Wtf\Dataset\Data::_(\Wtf\Dataset\Data::BOOLEAN),
		'md5pass' => \Wtf\Dataset\Data::_(\Wtf\Dataset\Data::STRING),
		'reset' => \Wtf\Dataset\Data::_(\Wtf\Dataset\Data::STRING),
//
		'parameters' => \Wtf\Dataset\Data::hasMany('user_parameter'),
		'groups' => \Wtf\Dataset\Data::hasMany('group_user'),
		'contacts' => \Wtf\Dataset\Data::hasMany('user_contact'),
		'posts' => \Wtf\Dataset\Data::hasMany('user_post'),
		'history' => \Wtf\Dataset\Data::hasMany('user_history'),
	],
	'index' => [
		'id:primary',
		'login:unique',
		'email:unique',
	]
];
