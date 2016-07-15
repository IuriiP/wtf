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
class Path implements \Wtf\Interfaces\Singleton, \Wtf\Interfaces\Collection {

	use \Wtf\Traits\Collection,
	 \Wtf\Traits\Singleton;

	/**
	 * Set predefined paths:
	 * 
	 * root
	 * temp
	 * upload
	 * config
	 * vendor
	 * private
	 * public
	 * cache
	 * 
	 */
	private function __construct() {
		$base = getenv('ROOT') || getenv('DOCUMENT_ROOT');
		$root = Resource::produce($base);

		$vendor = getenv('VENDOR');
		$this->offsetSet('vendor', $vendor ? Resource::produce($vendor) : Resource::produce($root, 'vendor'));
		$config = getenv('CONFIG');
		$this->offsetSet('config', $config ? Resource::produce($config) : Resource::produce($root, 'config'));
		$private = getenv('PRIVATE');
		$this->offsetSet('private', $private ? Resource::produce($private) : Resource::produce($root, 'private'));
		$public = getenv('PUBLIC');
		$this->offsetSet('public', $public ? Resource::produce($public) : Resource::produce($root, 'public'));
		$cache = getenv('CACHE');
		$this->offsetSet('cache', $cache ? Resource::produce($cache) : Resource::produce($root, 'cache'));

		$temp_dir = getenv('TEMP') || sys_get_temp_dir();
		$temp = Resource::produce($temp_dir);
		$this->offsetSet('temp', $temp);
		if($upload = getenv('UPLOAD')) {
			$this->offsetSet('upload', Resource::produce($upload));
		} else {
			$this->offsetSet('upload', $temp);
		}
	}

}
