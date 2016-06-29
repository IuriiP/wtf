<?php

/*
 * Copyright (C) 2016 Iurii Prudius <hardwork.mouse@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Wtf\Core;

use Wtf\Core\Resource;

/**
 * Description of Path
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
class Path implements \Wtf\Interfaces\Singleton, \Wtf\Interfaces\Container {

	use \Wtf\Traits\Container,
	 \Wtf\Traits\Singleton;

	/**
	 * Set predefined paths:
	 * 
	 * root = base
	 * temp = upload
	 * config
	 * vendor
	 * private
	 * public
	 * 
	 */
	private function __construct() {
		$root = Resource::produce(getenv('DOCUMENT_ROOT'));
		$this->offsetSet('root', $root);
		$this->offsetSet('base', $root);
		$this->offsetSet('vendor', Resource::produce($root, 'vendor'));
		$this->offsetSet('config', Resource::produce($root, 'config'));
		$this->offsetSet('private', Resource::produce($root, 'private'));
		$this->offsetSet('public', Resource::produce($root, 'public'));
		$this->offsetSet('cache', Resource::produce($root, 'cache'));

		$temp = Resource::produce(sys_get_temp_dir());
		$this->offsetSet('temp', $temp);
		$this->offsetSet('upload', $temp);
	}

}
