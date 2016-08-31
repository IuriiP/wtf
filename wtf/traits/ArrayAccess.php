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

namespace Wtf\Traits;

/**
 * ArrayAccess is implementation of \ArrayAccess & \IteratorAggregate.
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
trait ArrayAccess {
	
	private $_array = [];
	
	public function getIterator() {
		return new \ArrayIterator($this->_array);
	}

	public function offsetExists($offset) {
		return isset($this->_array[$offset]);
	}

	public function offsetGet($offset) {
		return isset($this->_array[$offset])?$this->_array[$offset]:null;
	}

	public function offsetSet($offset, $value) {
		if(is_null($offset)) {
			$this->_array[] = $value;
		} else {
			$this->_array[$offset] = $value;
		}
	}

	public function offsetUnset($offset) {
		unset($this->_array[$offset]);
	}

}
