<?php

return [
	'id' => \Wtf\Dataset\Data::_(\Wtf\Dataset\Data::PRIMARY),
	'name' => \Wtf\Dataset\Data::linkTo('translation:text'),
	'attributes' => \Wtf\Dataset\Data::_('SET:private:protected:user:group'),
];