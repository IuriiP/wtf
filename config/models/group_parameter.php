<?php

return [
	'id' => \Wtf\Dataset\Data::_(\Wtf\Dataset\Data::PRIMARY),
	'parameter' => \Wtf\Dataset\Data::linkTo('parameter'),
	'group' => \Wtf\Dataset\Data::linkTo('group'),
	'style' => \Wtf\Dataset\Data::_(['ENUM:integer:double:string:spatial:text:binary',"DEFAULT 'string'"]),
	'integer' => \Wtf\Dataset\Data::_(\Wtf\Dataset\Data::INTEGER),
	'double' => \Wtf\Dataset\Data::_(\Wtf\Dataset\Data::DOUBLE),
	'string' => \Wtf\Dataset\Data::_(\Wtf\Dataset\Data::STRING),
	'spatial' => \Wtf\Dataset\Data::_(\Wtf\Dataset\Data::SPATIAL),
	'text' => \Wtf\Dataset\Data::_(\Wtf\Dataset\Data::TEXT),
	'binary' => \Wtf\Dataset\Data::_(\Wtf\Dataset\Data::BINARY),
];
