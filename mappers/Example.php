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

namespace Mappers;

/**
 * Description of Example
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
class Example extends \Wtf\Core\DataMapper implements \Wtf\Interfaces\Writable {
	
	public function __construct() {
		$this->source = \Wtf\Core\Dataset::instance('');
	}

	public function __get($name) {
		
	}

	public function all($conditions) {
		
	}

	public function append($data) {
		
	}

	public function count($conditions) {
		
	}

	public function first($conditions) {
		
	}

	public function put($data) {
		
	}

	public function remove() {
		
	}
}
