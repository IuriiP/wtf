<?php

return [
	'login' => \Wtf\Dataset\Data::_([\Wtf\Dataset\Data::STRING, 'UNIQUE']),
	'email' => \Wtf\Dataset\Data::_([\Wtf\Dataset\Data::STRING, 'UNIQUE']),
	'valid' => \Wtf\Dataset\Data::_(\Wtf\Dataset\Data::BOOLEAN),
	'md5pass' => \Wtf\Dataset\Data::_(\Wtf\Dataset\Data::STRING),
	'reset' => \Wtf\Dataset\Data::_(\Wtf\Dataset\Data::STRING),
];
