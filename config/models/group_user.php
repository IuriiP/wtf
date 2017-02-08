<?php

return [
	'id' => \Wtf\Dataset\Data::_(\Wtf\Dataset\Data::PRIMARY),
	'group' => \Wtf\Dataset\Data::linkTo('group'),
	'user' => \Wtf\Dataset\Data::linkTo('user'),
	// user rights in group
	'rights' => \Wtf\Dataset\Data::_(['SET:read:write:edit:delete:moderate',"DEFAULT 'read'"]),
	// user markers on group
	'marks' => \Wtf\Dataset\Data::_(['SET:marked:first:favorite:observe',"DEFAULT ''"]),
];
