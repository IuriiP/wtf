<?php

/*
 * Copyright (C) 2016 IuriiP <hardwork.mouse@gmail.com>
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

/**
 * User is a basic class for the personality access.
 * 
 * Config for users may be:
 * string - the name of the descentant class for an accessing to users data
 * array|object - the container for the list of users
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
class User implements \Wtf\Interfaces\Pool, \Wtf\Interfaces\Collection, \Wtf\Interfaces\Configurable {

	use \Wtf\Traits\Pool,
		\Wtf\Traits\Collection,
		\Wtf\Traits\Configurable;

	public $id = null;

	public function __construct($id=null) {
		$resource = $this->config('resource');
		if($resource) {
			$source = Resource::produce($resource,['id'=>$id]);
			$this->set($source->get());
		}
	}
		
	private function _check($cred, \Traversable $list) {
		foreach($list as $id => $data) {
			$expect = \Wtf\Helper\Complex::eliminate($data, 'credentials');
			if(!array_diff_assoc($expect, $cred)) {
				$this->id = $id;
				$this->set($data);
				return;
			}
		}
	}

}
