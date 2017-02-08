<?php

return [
	'id' => \Wtf\Dataset\Data::_(\Wtf\Dataset\Data::PRIMARY),
	'created' => \Wtf\Dataset\Data::_(\Wtf\Dataset\Data::DATETIME),
	//
	'name' => \Wtf\Dataset\Data::linkTo('translation:text'),
	'master' => \Wtf\Dataset\Data::linkTo('user'),
	//
	'parameters' => \Wtf\Dataset\Data::hasMany('group_parameter','group'),
	'users' => \Wtf\Dataset\Data::hasMany('group_user','group'),
	'posts' => \Wtf\Dataset\Data::hasMany('group_post','group'),
];
