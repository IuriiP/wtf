<?php

return [
	'id' => \Wtf\Dataset\Data::_(\Wtf\Dataset\Data::PRIMARY),
	//
	'texts' => \Wtf\Dataset\Data::hasMany('language_translation:text', ['id' => 'translation']),
	//
	'text' => \Wtf\Dataset\Data::linkThrough('language_translation:text', ['id'=>'translation','language'=>  \Wtf\Core\I18n::current()])
];