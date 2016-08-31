<?php

return [
	'id' => \Wtf\Dataset\Data::_(\Wtf\Dataset\Data::PRIMARY),
	// keys
	'language' => \Wtf\Dataset\Data::linkTo('language'),
	'translation' => \Wtf\Dataset\Data::linkTo('translation'),
	// text
	'text' => \Wtf\Dataset\Data::_(\Wtf\Dataset\Data::TEXT),
];